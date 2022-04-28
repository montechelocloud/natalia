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
        return $this->ssvClient->sendData('POST', '', $complaints);
    }

    public function createComplaint(array $data)
    {
        return $this->ssvClient->sendData('POST', '', $data);
    }
}