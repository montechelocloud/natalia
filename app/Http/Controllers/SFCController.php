<?php

namespace App\Http\Controllers;

use App\Managers\DCFManager;
use App\Managers\SFCManager;
use App\Traits\CallControllerMethodTrait;
use App\Traits\RequestValidator;
use Illuminate\Http\Request;

class SFCController extends Controller
{
    use CallControllerMethodTrait, RequestValidator;

    private $sfcManager;
    private $dcfManager;

    public function __construct(SFCManager $sfcManager, DCFManager $dcfManager)
    {
        $this->sfcManager = $sfcManager;
        $this->dcfManager = $dcfManager;
    }

    /**
     * Obtiene todas las quejas.
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co> 
     *
     * @return Illuminate\Http\Response
     */
    public function getComplaints()
    {
        $response = $this->sfcManager->consultComplaints();

        return response()->json($response, 200);
    }

    /**
     * Obtiene la queja solicitada.
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co>
     *
     * @return Illuminate\Http\Response
     */
    public function getComplaint($complaintId)
    {
        $response = $this->sfcManager->consultComplaint($complaintId);

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
        $this->dataValidate($request->all(), [
            'pqrs' => 'required|array'
        ]);

        $response = $this->sfcManager->synchronizeComplaints($request->pqrs);

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
        $response = $this->sfcManager->getFile($fileId);
        
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
        $response = $this->sfcManager->getComplaintFiles($complaintId);
        
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
        $this->dataValidate($request->all(), [
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

        $response = $this->sfcManager->createComplaint($request);

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
        $this->dataValidate($request->all(), [
            'file'          => 'required|file',
            'codigo_queja'  => 'required|string',
            'type'          => 'required|string'
        ]);

        $response = $this->sfcManager->fileUpload($request);

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
        $this->dataValidate($request->all(), [
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
        
        $response = $this->sfcManager->updateComplaint($request);

        // if ($response->status_code == 200) {

        //     $dcfResponse = $this->callControllerMethod('DCFController', 'updateComplaint', $data);
        //     //! No esta definida la respuesta de la Defensoria
        //     //! Tener en cuenta para darle el respectivo tratamiento
        //     //! para notificar al crm { -->
        //     if ($dcfResponse->status_code == 200) {
        //         $response->dcfResponse = $dcfResponse;
        //     }
        //     //! --< } 
        // }

        return response()->json($response, $response->status_code ?? 200);
    }
}
