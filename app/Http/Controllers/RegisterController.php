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
