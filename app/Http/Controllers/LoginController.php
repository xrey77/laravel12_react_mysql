<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use PragmaRX\Google2FA\Google2FA;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;
        $secret = config('services.api_service.key');
        $issuer = config('services.issuer_service.key');
        $user = User::where(\DB::raw('BINARY `username`'),$username)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {

                $credentials = ['username' => $username, 'password' => $password];                
                if (Auth::attempt($credentials)) {
                    $user->tokens()->delete();
                    $token = $user->createToken($secret)->plainTextToken;       
                    return response()->json([                    
                        'message' => 'Login successfull.',
                        'id' => $user->id,
                        'username' => $username,                    
                        'password' => $password,
                        'roles' => $user->roles,
                        'profilepic' => $user->profilepic,
                        'qrcodeurl' => $user->qrcodeurl,
                        'isactivated' => $user->isactivated,
                        'isblocked' => $user->isactivated,
                        'token' => $token
                    ],200);
                }



        
            } else {
                return response()->json(['message' => 'Invalid password, try again.'],404);
            }
        } else {
            return response()->json(['message' => 'Username not found, please register first.'],404);
        }
    }

    // protected function respondWithToken($token)
    // {
    //     return [
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         'expires_in' => auth()->factory()->getTTL() * 60
    //     ];
    // }    

    // protected function createNewToken($token){
    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         'expires_in' => auth()->factory()->getTTL() * 60,
    //         'user' => auth()->user()
    //     ]);
    // } 
}
