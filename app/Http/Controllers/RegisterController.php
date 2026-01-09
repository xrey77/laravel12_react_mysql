<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class RegisterController extends Controller
{

    /**
     * Register a new user.
     */
    #[OA\Post(
        path: "/api/register",
        summary: "Register a new user",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "username", "password"],
                properties: [
                    new OA\Property(property: "firstname", type: "string", example: "John"),
                    new OA\Property(property: "lastname", type: "string", example: "Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "mobile", type: "string", example: "1234567890"),
                    new OA\Property(property: "username", type: "string", example: "johndoe"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Secret123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User registered successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "User registered successfully."),
                        new OA\Property(property: "firstname", type: "string"),
                        new OA\Property(property: "lastname", type: "string"),
                        new OA\Property(property: "email", type: "string"),
                        new OA\Property(property: "mobile", type: "string"),
                        new OA\Property(property: "username", type: "string"),
                        new OA\Property(property: "password", type: "string")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Bad Request - Email or Username already taken",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Email Address is already taken.")
                    ]
                )
            )
        ]
    )]    
    public function register(Request $request) 
    {

        $validated = $request->validate([
            'email' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $email = $request->email;
        $mobile = $request->mobile;
        $username = $request->username;
        $password = Hash::make($request->password);

        $emailUser = User::where('email', $email)->first();
        if ($emailUser) {
            return response()->json(['message' => 'Email Address is already taken.'],400);
        }

        $userName = User::where('username', $username)->first();
        if ($userName) {
            return response()->json(['message' => 'Username is already taken.'],400);
        }

        try {
            $roleId = 2;
            $user = User::create ([
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'mobile' => $mobile,
                'username' => $username,
                'password' => $password,
                'role_id' => $roleId,
                'profilepic' => 'http://127.0.0.1:8000/images/pix.png',
            ]);
            $user->roles()->attach($roleId);

        } 
        catch (\Exception $e) 
        {            
        }
        
        $secret = "";
        $user = User::where('username', $username)->first();
        $credentials = ['username' => $username, 'password' => $password];                
        if (Hash::check($request->password, $user->password)) {
            Auth::attempt($credentials);
            $secret = encrypt($user->two_factor_secret);        
            $user->two_factor_secret = $secret;
            $user->save();                    
        }
        
        return response()->json([
            'message' => 'User registered successfully.',
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email'=> $email,
            'mobile' => $mobile,
            'username' => $username,
            'password' => $password],201);
    }
}
