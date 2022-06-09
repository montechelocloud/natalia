<?php

namespace App\Managers;

use App\Http\Clients\SFCClient;
use Illuminate\Support\Carbon;

class SFCManager
{
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

    public function consultComplaints() : object
    {
        return $this->sfcClient->sendData('GET', 'queja/');
    }

    public function consultComplaint($complaintId) : object
    {
        return $this->sfcClient->sendData('POST', "queja/$complaintId/");
    }

    public function getComplaintsCode(array $complaints) : array
    {
        $complaintCodes = [];
        foreach ($complaints as $key => $complaint) {
            $complaintCodes = $complaint->codigo_queja;
        }

        return $complaintCodes;
    }

    public function nextPage(string $nextPage)
    {
        return $this->sfcClient->sendData('GET', $nextPage);
    }

    public function synchronizeComplaints(array $complaintsCodes)
    {
        $data = [
            'json' => ['pqrs' => $complaintsCodes]
        ];

        return $this->sfcClient->sendData('POST', 'complaint/ack', $data);
    }

    public function getFile(int $fileId)
    {
        return $this->sfcClient->sendData('GET', "storage/$fileId/");
    }

    public function getComplaintFiles(string $complaintId)
    {
        $response = $this->sfcClient->sendData('GET', "storage/?codigo_queja__codigo_queja=$complaintId");
        
        return response()->json($response, 200);
    }

    public function createComplaint($request)
    {
        $data = [
            'json' => array_merge([
                'tipo_entidad' => $this->entityType,
                'entidad_cod' => $this->entityCode,
                'codigo_queja' => $this->entityType . $this->entityCode . $request->codigo_queja
            ], $request->except('codigo_queja'))
        ];

        return $this->sfcClient->sendData('POST', 'queja/', $data);
    }

    public function fileUpload($request)
    {
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
            'payload' => $request->only('type', 'codigo_queja'),
            'multipart' => $multipartData,
        ];

        return $this->sfcClient->sendData('POST', 'storage/', $data);
    }

    public function updateComplaint($request)
    {
        $data = [
            'json' => array_merge([
                'tipo_entidad' => $this->entityType,
                'entidad_cod' => $this->entityCode,
                'codigo_queja' => $request->codigo_queja
            ], $request->except('codigo_queja'))
        ];
        
        return $this->sfcClient->sendData('PUT', "queja/{$request->codigo_queja}/", $data);
    }

    /**
     * Loguea al web service en la SFC (Superintendencia Financiera de Colombia).
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co> 
     *
     * @return void
     */
    public function login()
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
    public function refresh()
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
}
