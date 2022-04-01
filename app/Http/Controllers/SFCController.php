<?php

namespace App\Http\Controllers;

use App\Http\Clients\SFCClient;
use App\Traits\CallControllerMethodTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class SFCController extends Controller
{
    use CallControllerMethodTrait;

    private $sfcClient;
    private $entityType;
    private $entityCode;

    public function __construct(SFCClient $sfcClient)
    {
        $this->sfcClient = $sfcClient;
        $this->entityType = (int) env('SFC_ENTITY_TYPE');
        $this->entityCode = env('SFC_ENTITY_CODE');

        $this->verifyAccesses();
    }

    /**
     * Loguea al web service en la SFC (Superintendencia Financiera de Colombia).
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co> 
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

        $response = (new SFCClient(false))->sendData('POST', 'login', $data, false);

        $this->setAccess($response);        
    }

    /**
     * Refresca el token de acceso a la SFC (Superintendencia Financiera de Colombia).
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co> 
     *
     * @return void
     */
    private function refresh()
    {
        $data = [
            'json' => [
                'refresh' => session('refresh')['token']
            ]
        ];

        $response = $this->sfcClient->sendData('POST', 'token/refresh', $data);

        if (isset($response->status_code)) {
            $this->login();
        }

        $this->setAccess($response);
    }

    /**
     * Obtiene todas las quejas.
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co> 
     *
     * @return Illuminate\Http\Response
     */
    public function getComplaints()
    {
        $response = $this->sfcClient->sendData('GET', 'queja/');

        return response()->json($response, 200);
    }

    /**
     * Obtiene la queja solicitada.
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co>
     *
     * @param integer $complaintId
     * @return Illuminate\Http\Response
     */
    public function getComplaint(int $complaintId)
    {
        $response = $this->sfcClient->sendData('GET', "queja/$complaintId/");

        return response()->json($response, $response->status_code ?? 200);
    }

    /**
     * Sincroniza los pqrs.
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co>
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function ack(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pqrs' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 200);
        }
        
        $data = [
            'json' => $request->only('pqrs')
        ];

        $response = $this->sfcClient->sendData('POST', 'complaint/ack', $data);

        return response()->json($response, 200);
    }

    /**
     * Obtiene el archivo solicitado.
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co>
     *
     * @param integer $fileId 
     * @return Illuminate\Http\Response
     */
    public function getFile(int $fileId)
    {
        $response = $this->sfcClient->sendData('GET', "storage/$fileId/");
        
        return response()->json($response, $response->status_code ?? 200);
    }
    
    /**
     * Obtiene los archivos de las quejas.
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co>
     *
     * @param string $complaintId
     * @return void
     */
    public function getComplaintFiles(string $complaintId)
    {
        $response = $this->sfcClient->sendData('GET', "storage/?codigo_queja__codigo_queja=$complaintId");
        
        return response()->json($response, 200);
    } 

    /**
     * Crear queja.
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co>
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function createComplaint(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_queja'      => 'required|string',
            'codigo_pais'       => 'required|numeric',
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
            'anexo_queja'       => 'required|boolean|numeric',
            'ente_control'      => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 200);
        }

        $data = [
            'json' => array_merge([
                'tipo_entidad' => $this->entityType,
                'entidad_cod' => $this->entityCode,
                'codigo_queja' => $this->entityType . $this->entityCode . $request->codigo_queja
            ], $request->except('codigo_queja'))
        ];

        $response = $this->sfcClient->sendData('POST', 'queja/', $data);

        return response()->json($response, $response->status_code ?? 200);
    }

    /**
     * Carga un archivo.
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co>
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function fileUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file'          => 'required|file',
            'codigo_queja'  => 'required|string',
            'type'          => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 200);
        }

        $multipartData = [];

        foreach ($request->all() as $key => $value) {
            if ($key == 'file') {
                $file = $request->file('file');
                $multipartData[] = [
                    'name' => $key,
                    'contents' => $file->getContent(),
                    'filename' => $file->getClientOriginalName(),
                ]; 
            } else {
                $multipartData[] = ['name' => $key, 'contents' => $value]; 
            }
        }

        $data = [
            'payload' => $request->only('codigo_queja', 'type'),
            'multipart' => $multipartData,
        ];

        $response = $this->sfcClient->sendData('POST', 'storage/', $data);

        return response()->json($response, $response->status_code ?? 200);
    }

    /**
     * Actualizar una queja
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co>
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function updateComplaint(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json($validator->errors(), 200);
        }

        $data = [
            'json' => $request->all()
        ];
        
        $response = $this->sfcClient->sendData('PUT', "queja/{$request->codigo_queja}/", $data);

        if ($response->status_code == 200) {

            $dcfResponse = $this->callControllerMethod('DCFController', 'updateComplaint', $data);
            //! No esta definida la respuesta de la Defensoria
            //! Tener en cuenta para darle el respectivo tratamiento
            //! para notificar al crm { -->
            if ($dcfResponse->status_code == 200) {
                $response->dcfResponse = $dcfResponse;
            }
            //! --< } 
        }

        return response()->json($response, $response->status_code);
    }

    /**
     * Verifica que la caducidad de los tokens, para solicitarlos de nuevo.
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co>
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
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co>
     *
     * @param object $response
     * @return void
     */
    private function setAccess(object $response)
    {
        if (isset($response->refresh) && isset($response->access)) {
            session([
                'refresh' => [
                    'token' => $response->refresh,
                    'expires' => now()->addHours(12)
                ],
                'access' => [
                    'token' => $response->access,
                    'expires' => now()->addMinutes(30)
                ]
            ]);
        }
    }
}
