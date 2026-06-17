<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        $credentials=$request->only(
            'email',
            'password'
        );

        if(!$token=auth('api')->attempt($credentials)){

            return response()->json([
                'message'=>'Login gagal'
            ],401);

        }

        return response()->json([

            'token'=>$token,

            'user'=>auth('api')->user()

        ]);

    }

    public function logout()
    {

        auth('api')->logout();

        return response()->json([
            'message'=>'Logout berhasil'
        ]);

    }

}