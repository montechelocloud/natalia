<?php

namespace App\Http\Controllers;

use App\Http\Clients\SFCClient;
use App\Http\Requests\AckRequest;
use App\Http\Requests\ComplaintCreateRequest;
use App\Http\Requests\ComplaintUpdateRequest;
use App\Http\Requests\FileUploadRequest;
use App\Traits\CallControllerMethodTrait;
use App\Traits\LogFailedRequestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SFCController extends Controller
{
    use LogFailedRequestTrait, CallControllerMethodTrait;

    private $sfcClient;

    public function __construct()
    {
        $this->sfcClient = new SFCClient;

        $this->verifyAccesses();
    }

    /**
     * Loguea al web service en la SFC (Superintendencia Financiera de Colombia).
     * @author Edwin David Sanchez Balbin 
     *
     * @return void
     */
    private function login()
    {
        $data = [
            'json' => [
                'username' => env('SFC_USERNAME'),
                'password' => env('SFC_PASSWORD')
            ]
        ];

        $response = $this->sfcClient->sendData('POST', 'login', $data, false);

        $this->setAccess($response);

        $this->saveLogFailedRequest('',$response);
    }

    /**
     * Refresca el token de acceso a la SFC (Superintendencia Financiera de Colombia).
     * @author Edwin David Sanchez Balbin 
     *
     * @return void
     */
    private function refresh()
    {
        $data = [
            'json' => [
                'refresh' => session('refresh')
            ]
        ];

        $response = $this->sfcClient->sendData('POST', 'token/refresh', $data);

        $this->setAccess($response);
        
        $this->saveLogFailedRequest('', $response);
    }

    /**
     * Obtiene todas las quejas.
     * @author Edwin David Sanchez Balbin 
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function getComplaints(Request $request)
    {
        $response = $this->sfcClient->sendData('GET', 'queja');

        $this->saveLogFailedRequest($request->path(), $response);

        return response()->json($response);
    }

    /**
     * Obtiene la queja solicitada.
     * @author Edwin David Sanchez Balbin
     *
     * @param integer $complaintId
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function getComplaint(int $complaintId, Request $request)
    {
        $response = $this->sfcClient->sendData('POST', "queja/$complaintId");

        $this->saveLogFailedRequest($request->path(), $response, compact('complaintId'));

        return response()->json($response);
    }

    /**
     * Sincroniza los pqrs.
     * @author Edwin David Sanchez Balbin
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function ack(AckRequest $request)
    {
        $data = [
            'json' => $request->only('pqrs')
        ];

        $response = $this->sfcClient->sendData('POST', 'complaint/ack', $data);

        $this->saveLogFailedRequest($request->path(), $response, $data);

        return response()->json($response);
    }

    /**
     * Obtiene los archivos de las quejas.
     * @author Edwin David Sanchez Balbin
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function getFiles(Request $request)
    {
        $response = $this->sfcClient->sendData('GET', 'storage');

        $this->saveLogFailedRequest($request->path(), $response);

        return response()->json($response);
    }

    /**
     * Crear queja.
     * @author Edwin David Sanchez Balbin
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function createComplaint(ComplaintCreateRequest $request)
    {
        $data = [
            'json' => $request->all()
        ];

        $response = $this->sfcClient->sendData('POST', 'queja', $data);

        $this->saveLogFailedRequest($request->path(), $response, $data);

        return response()->json($response);
    }

    /**
     * Carga un archivo.
     * @author Edwin David Sanchez Balbin
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function fileUpload(FileUploadRequest $request)
    {
        $data = [
            'payload' => $request->only('codigo_queja', 'type'),
            'multipart' => $request->all()
        ];

        $response = $this->sfcClient->sendData('POST', 'storage', $data);

        $this->saveLogFailedRequest($request->path(), $response, $data);

        return response()->json($response);
    }

    /**
     * Actualizar una queja
     * @author Edwin David Sanchez Balbin
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function updateComplaint(ComplaintUpdateRequest $request)
    {
        $data = [
            'json' => $request->all()
        ];
        
        $response = $this->sfcClient->sendData('POST', "queja/{$request->codigo_queja}", $data);

        $this->callControllerMethod('DCFController', 'updateComplaint', $data);

        $this->saveLogFailedRequest($request->path(), $response, $data);

        return response()->json($response);
    }

    /**
     * Verifica que la caducidad de los tokens, para solicitarlos de nuevo.
     * @author Edwin David Sanchez Balbin
     *
     * @return void
     */
    private function verifyAccesses()
    {
        if (session()->has('refresh') && session()->has('access')) {
            $currentDateTime = Carbon::now();

            if ($currentDateTime->greaterThanOrEqualTo(session('refresh')['expires'])) {
                $this->login();
            } else {
                if ($currentDateTime->greaterThanOrEqualTo(session('access')['expires'])) {
                    $this->refresh();
                }
            }

        } else {
            $this->login();
        }
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
                'refresh' => [
                    'token' => $response->refresh,
                    'expires' => Carbon::now()->addHours(12)
                ],
                'access' => [
                    'token' => $response->access],
                    'expires' => Carbon::now()->addMinutes(30)
            ]);
        } 
    }
}
