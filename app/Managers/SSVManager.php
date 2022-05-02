<?php

namespace App\Managers;

use App\Http\Clients\SSVClient;

class SSVManager
{
    private $ssvClient;

    public function __construct(SSVClient $ssvClient)
    {
        $this->ssvClient = $ssvClient;
    }

    public function massCreationOfComplaints(array $complaints)
    {
        $data = [
            'json' => [
                'results' => $complaints
            ]
        ];
        return $this->ssvClient->sendData('POST', 'sfc_all', $data);
    }

    public function createComplaint(array $data)
    {
        return $this->ssvClient->sendData('POST', '', $data);
    }
}