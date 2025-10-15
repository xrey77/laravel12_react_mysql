<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
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

        // $emailUser = User::where('email', $email)->first();
        // if ($emailUser) {
        //     return response()->json(['statuscode' => 400, 'message' => 'Email Address is already taken.']);            
        // }

        // $userName = User::where('username', $username)->first();
        // if ($userName) {
        //     return response()->json(['statuscode' => 400, 'message' => 'Username is already taken.']);            
        // }

        // Auth:login()
        // $secret = encrypt($user->two_factor_secret);
        // $user->two_factor_secret = $secret;
        // $user->two_factor_recovery_codes = $recovery_code;
        // $user->save();                    



        try {
            User::create ([
                'lastname' => $lastname,
                'firstname' => $firstname,
                'email' => $email,
                'mobile' => $mobile,
                'username' => $username,
                'password' => $password,
                'roles' => 'USER',
                'profilepic' => 'http://127.0.0.1:8000/images/pix.png',
            ]);
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
            // $recovery_code = encrypt($user->two_factor_recovery_codes);
            $user->two_factor_secret = $secret;
            // $user->two_factor_recovery_codes = $recovery_code;
            $user->save();                    
        }
        
        return response()->json([
            'statuscode' => 201,
            'message' => 'User registered successfully.',
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email'=> $email,
            'mobile' => $mobile,
            'username' => $username,
            'password' => $password]);
    }
}
