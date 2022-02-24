<?php

namespace App\Http\Controllers;

use App\Http\Clients\DCFClient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DCFController extends Controller
{
    private $dcfClient;

    public function __construct(DCFClient $dcfClient)
    {
        $this->dcfClient = $dcfClient;

        $this->verifyAccesses();
    }

    /**
     * Autentica (loguea) al web service en la plataforma del DCF (Defensor al Consumidor)
     * @author Edwin David Sanchez Balbin
     *
     * @param Request $request
     * @return void
     */
    public function authenticate()
    {
        $data = [
            'json' => [
                'username' => env('DCF_USERNAME'),
                'password' => env('DCF_PASSWORD')
            ]
        ];

        $response = $this->dcfClient->sendData('POST', '', $data);

        //! No se a definido metodo de autentificacion en la Defensoria
        //! No se sabe si va ser por token y si va tener tiempo de expiracion { -->
            $this->setAccess($response);
        //! <-- }
        
        $this->saveLogFailedRequest('', $response);
    }

    /**
     * Actualiza la queja en el DCF
     * @author Edwin David Sanchez Balbin
     *
     * @param array $data
     * @return object
     */
    public function updateComplaint(array $data) : object
    {
        $response = $this->dcfClient->sendData('POST', '', $data);

        $this->saveLogFailedRequest('', $response, $data);

        return $response;
    }

    /**
     * Establece los tokens de acceso en la session y en el momento que caducan.
     * @author Edwin David Sanchez Balbin
     *
     * @param object $response
     * @return void
     */
    private function setAccess(object $response)
    {
        if ($response->status_code == 200) {
            session([
                'dcf' => [
                    'token' => $response->token,
                    'expires' => Carbon::now()->addHours(12)
                ]
            ]);
        }
    }

    /**
     * Verifica que la caducidad de los tokens, para solicitarlos de nuevo.
     * @author Edwin David Sanchez Balbin
     *
     * @return void
     */
    private function verifyAccesses()
    {
        if (session()->has('dcf')) {
            $currentDateTime = Carbon::now();

            if ($currentDateTime->greaterThanOrEqualTo(session('dcf')['expires'])) {
                $this->login();
            }

        } else {
            $this->login();
        }
    }
}
