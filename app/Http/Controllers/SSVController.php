<?php

namespace App\Http\Controllers;

use App\Http\Clients\SSVClient;
use Illuminate\Http\Request;

class SSVController extends Controller
{
    private $ssvClient;

    public function __construct()
    {
        $this->ssvClient = new SSVClient;    
    }

    /**
     * Crea una queja en el SSV
     * @author Edwin David Sanchez Balbin
     *
     * @param Request $request
     * @return void
     */
    public function createComplaint(Request $request)
    {
        $request->validate();

        $response = $this->ssvClient->sendData('POST', '', []);

        $this->saveLogFailedRequest($request->path(), $response, []);

        return response()->json($response);
    }

    /**
     * Actualia una queja en el SSV
     * @author Edwin David Sanchez Balbin
     *
     * @param Request $request
     * @return void
     */
    public function updateComplaint(Request $request)
    {
        $request->validate();

        $response = $this->ssvClient->sendData('POST', '', []);

        $this->saveLogFailedRequest($request->path(), $response, []);

        return response()->json($response);
    }
}
