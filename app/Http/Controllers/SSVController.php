<?php

namespace App\Http\Controllers;

use App\Http\Clients\SSVClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SSVController extends Controller
{
    private $ssvClient;

    public function __construct(SSVClient $ssvClient)
    {
        $this->ssvClient = $ssvClient;    
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
        dd($request->all());
        // $validator = Validator::make($request->all(), []);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 200);
        // }

        $response = $this->ssvClient->sendData('POST', '', []);

        return response()->json($response, $response->status());
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
        dd($request->all());
        // $validator = Validator::make($request->all(), []);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 200);
        // }

        $response = $this->ssvClient->sendData('POST', '', []);

        return response()->json($response, $response->status());
    }
}
