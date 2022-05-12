<?php

namespace App\Http\Controllers;

use App\Managers\SSVManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SSVController extends Controller
{
    private $ssvManager;

    public function __construct(SSVManager $ssvManager)
    {
        $this->ssvManager = $ssvManager;    
    }

    /**
     * Crea una queja en el SSV
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co>
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function createComplaint(Request $request)
    {
        // $validator = Validator::make($request->all(), []);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 200);
        // }

        $response = $this->ssvManager->createComplaint($request->all());

        return response()->json($response, $response->status());
    }

    /**
     * Actualia una queja en el SSV
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co>
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    // public function updateComplaint(Request $request)
    // {
        // $validator = Validator::make($request->all(), []);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 200);
        // }

    //     $response = $this->ssvClient->sendData('POST', '', []);

    //     return response()->json($response, $response->status());
    // }
}
