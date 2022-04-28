<?php

namespace App\Managers;

use App\Http\Clients\DCFClient;

class DCFManager
{
    private $dcfClient;

    public function __construct(DCFClient $dcfClient)
    {
        $this->dcfClient = $dcfClient;
    }

    public function updateComplaint($request)
    {
        $data = [
            'json' => array_merge([
                'tipo_entidad' => $this->entityType,
                'entidad_cod' => $this->entityCode,
                'codigo_queja' => $this->entityType . $this->entityCode . $request->codigo_queja
            ], $request->except('codigo_queja'))
        ];

        return $this->dcfClient->sendData('POST', '', $data);
    }
}