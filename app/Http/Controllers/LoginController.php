<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Arr;
use OpenApi\Attributes as OA;

class LoginController extends Controller
{

/**
 * Handle user authentication.
 */
    #[OA\Post(
        path: "/api/login",
        summary: "User Login",
        description: "Authenticates user and returns a plain text token.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["username", "password"],
                properties: [
                    new OA\Property(property: "username", type: "string", example: "johndoe"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "secret123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Login successfull."),
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "username", type: "string", example: "johndoe"),
                        new OA\Property(property: "roles", type: "string", example: "admin"),
                        new OA\Property(property: "token", type: "string", example: "1|abcdef123456...")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Invalid credentials or user not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Username not found, please register first.")
                    ]
                )
            )
        ]
    )]    
    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;
        $secret = config('services.api_service.key');
        $issuer = config('services.issuer_service.key');
        $user = User::where(\DB::raw('BINARY `username`'),$username)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $rolename = $user->roles->pluck('name')->first();
                $credentials = ['username' => $username, 'password' => $password];                
                if (Auth::attempt($credentials)) {
                    $user->tokens()->delete();
                    $token = $user->createToken($secret)->plainTextToken;       
                    return response()->json([                    
                        'message' => 'Login successfull.',
                        'id' => $user->id,
                        'username' => $username,                    
                        'password' => $password,
                        'roles' => $rolename,
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
