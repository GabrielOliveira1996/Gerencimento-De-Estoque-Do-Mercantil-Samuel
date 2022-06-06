<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ApiAuthController extends Controller
{
    

    public function login(Request $request){

        $arrayAuth['error'] = '';

        $creds = $request->only('email', 'password');

        if(Auth::attempt($creds)){
            
            $user = User::where('email', $creds['email'])->first();

            $hash = time().rand(0, 9999);

            $token = $user->createToken($hash)->plainTextToken;
            
            $arrayAuth['success'] = $token;

        }else{
            $arrayAuth['error'] = 'E-mail e/ou senha incorretos.';
        }

        return $arrayAuth;
    }
}
