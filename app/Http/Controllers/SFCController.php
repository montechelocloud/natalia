<?php

namespace App\Http\Controllers;

use App\HttpClients\DCFClient;
use App\HttpClients\SFCClient;
use App\Traits\LogFailedRequestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SFCController extends Controller
{
    use LogFailedRequestTrait;

    private $sfcClient;
    private $dcfClient;

    public function __construct(SFCClient $sfcClient, DCFClient $dcfClient)
    {
        $this->sfcClient = $sfcClient;
        $this->dcfClient = $dcfClient;

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
    public function ack(Request $request)
    {
        $request->validate([
            'pqrs' => 'required|array'
        ]);

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
    public function createComplaint(Request $request)
    {
        $request->validate([
            'codigo_queja'      => 'required|string',
            'codigo_pais'       => 'required|string',
            'departamento_cod'  => 'required|string',
            'municipio_cod'     => 'required|string',
            'canal_cod'         => 'required|numeric',
            'producto_cod'      => 'required|numeric',
            'macro_motivo_cod'  => 'required|numeric',
            'fecha_creacion'    => 'required|date',
            'nombres'           => 'required|string',
            'tipo_id_CF'        => 'required|numeric',
            'numero_id_CF'      => 'required|string',
            'tipo_persona'      => 'required|numeric',
            'insta_recepcion'   => 'required|numeric',
            'punto_recepcion'   => 'required|numeric',
            'admision'          => 'required|numeric',
            'texto_queja'       => 'required|string',
            'anexo_queja'       => 'required|boolean',
            'ente_control'      => 'required|numeric',
        ]);

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
    public function fileUpload(Request $request)
    {
        $request->validate([
            'file'          => 'required|file',
            'codigo_queja'  => 'required|string',
            'type'          => 'required|string'
        ]);

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
    public function updateComplaint(Request $request)
    {
        $request->validate([
            'codigo_queja'              => 'required|string',
            'sexo'                      => 'required|numeric',
            'lgbtiq'                    => 'required|numeric',
            'condicion_especial'        => 'required|numeric',
            'canal_cod'                 => 'required|numeric',
            'producto_cod'              => 'required|numeric',
            'macro_motivo_cod'          => 'required|numeric',
            'estado_cod'                => 'required|numeric',
            'fecha_actualizacion'       => 'required|date',
            'producto_digital'          => 'required|numeric',
            'a_favor_de'                => 'required|numeric',
            'aceptacion_queja'          => 'required|numeric',
            'rectificacion_queja'       => 'required|numeric',
            'desistimiento_queja'       => 'required|numeric',
            'prorroga_queja'            => 'required|numeric',
            'admision'                  => 'required|numeric',
            'documentacion_rta_final'   => 'required|boolean',
            'anexo_queja'               => 'required|boolean',
            'fecha_cierre'              => 'required|date',
            'tutela'                    => 'required|numeric',
            'ente_control'              => 'required|numeric',
            'marcacion'                 => 'required|numeric',
            'queja_expres'              => 'required|numeric',
        ]);

        $data = [
            'json' => $request->all()
        ];
        
        $response = $this->sfcClient->sendData('POST', "queja/{$request->codigo_queja}", $data);

        // $this->dcfClient->sendData('POST', "queja/{$request->codigo_queja}", $data);

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
