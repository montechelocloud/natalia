<?php

namespace App\Http\Controllers;

use App\HttpClients\DCFClient;
use Illuminate\Http\Request;

class DCFController extends Controller
{
    private $dcfClient;

    public function __construct()
    {
        $this->dcfClient = new DCFClient;
    }

    /**
     * Autentica (loguea) al SSV en la plataforma del DCF
     * @author Edwin David Sanchez Balbin
     *
     * @param Request $request
     * @return void
     */
    public function authenticate(Request $request)
    {
        $request->validate();
        $response = $this->dcfClient->sendData('POST', '', []);
        return response()->json($response);
    }

    /**
     * Actualiza la queja en el DCF
     * @author Edwin David Sanchez Balbin
     *
     * @param array $data
     * @return void
     */
    public function updateComplaint(array $data)
    {
        $response = $this->dcfClient->sendData('POST', '', $data);

        $this->saveLogFailedRequest('', $response, $data);

        return response()->json($response);
    }
}
