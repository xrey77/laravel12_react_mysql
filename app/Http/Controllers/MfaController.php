<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MfaController extends Controller
{
    public function mfalogin(Request $request)
    {
        $otp = $request->otpcode;
        return response()->json(['statuscode' => '200', 'message' => 'OTP Successful...']);
    }
}
