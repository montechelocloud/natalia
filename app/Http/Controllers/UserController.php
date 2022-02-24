<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * Verifica que el usuario exista y le regresa un token de acceso.
     * @author Edwin David Sanchez Balbin
     *
     * @param Request $request
     * @return void
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'email'         => 'required|email',
            'password'      => 'required|min:8',
        ]);

        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        
        return response()->json(compact('token'), 200);
    }
}
