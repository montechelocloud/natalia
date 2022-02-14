<?php

namespace App\Http\Controllers;

use App\Traits\{LogFailedRequestTrait, RequestServiceTrait};
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SFCController extends Controller
{
    use RequestServiceTrait, LogFailedRequestTrait;

    private $client;
    private $payload;

    public function __construct(Client $client)
    {
        $this->client = $client;

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
        $this->payload = ['username' => env('SFC_USERNAME'), 'password' => env('SFC_PASSWORD')];

        $response = $this->jsonRequest('POST', 'login', $this->payload, false);

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
        $response = $this->jsonRequest('POST', 'token/refresh', ['refresh' => session('refresh')]);

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
        $this->payload = 'api/queja';

        $response = $this->jsonRequest('GET', 'queja');

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
        $this->payload = "api/queja/$complaintId";

        $response = $this->jsonRequest('POST', "queja/$complaintId");

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
    public function ack(Request $request)
    {
        $this->payload = $request->only('pqrs');

        $response = $this->jsonRequest('POST', 'complaint/ack', $this->payload);

        $this->saveLogFailedRequest($request->path(), $response, $this->payload);

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
        $this->payload = 'api/storage';

        $response = $this->jsonRequest('GET', 'storage');

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
    public function createComplaint(Request $request)
    {
        $this->payload = $request->all();

        $response = $this->jsonRequest('POST', 'queja', $this->payload);

        $this->saveLogFailedRequest($request->path(), $response, $this->payload);

        return response()->json($response);
    }

    /**
     * Carga un archivo.
     * @author Edwin David Sanchez Balbin
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function fileUpload(Request $request)
    {
        $this->payload = $request->only('codigo_queja', 'type');

        $response = $this->jsonRequest('POST', 'storage', $request->all());

        $this->saveLogFailedRequest($request->path(), $response, $this->payload);

        return response()->json($response);
    }

    /**
     * Actualizar una queja
     * @author Edwin David Sanchez Balbin
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function updateComplaint(Request $request)
    {
        $this->payload = $request->all();

        $response = $this->jsonRequest('POST', "queja/{$request->codigo_queja}", $this->payload);

        $this->saveLogFailedRequest($request->path(), $response, $this->payload);

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
