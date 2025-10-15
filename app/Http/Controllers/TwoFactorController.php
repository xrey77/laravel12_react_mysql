<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Exception;


class TwoFactorController extends Controller
{
    public function show(Request $request, Google2FA $google2fa)
    {
        if (Auth::guard('sanctum')->check()) {


            $user = auth('sanctum')->user();
            // $user = $request->user();

            // Use the method on the user model, not the provider
            $svg = $user->twoFactorQrCodeSvg();

            // // $qrcode = $user->twoFactorQrCodeSvg();
            // // $qurl = auth()->user()->twoFactorQrCodeUrl();
            // $secret = $user->two_factor_secret();
            // return response()->json($secret);
            // $mfa = $this->twoFactorProvider->generateTwoFactorSecretAndRecoveryCodes($user);
            // $user->save();
    
            // $qrCode = base64_encode(
            //     QrCode::format('png')
            //         ->size(200)
            //         ->generate($user->twoFactorQrCodeUrl())
            // );
            return response()->json(['mfa' => auth()->user()->twoFactorQrCodeUrl() ]);

            // return response()->json([
            //     'two_factor_secret' => encrypt($user->two_factor_secret),
            //     'recovery_codes' => (array) json_decode(decrypt($user->two_factor_recovery_codes)),
            //     'qr_code' => "data:image/png;base64,{$qrCode}",
            // ]); 

            // $qrCodeUrl = app(TwoFactorQrCodeGenerator::class)->__invoke($request, $google2fa);
            // $qrCode = base64_encode(QrCode::format('svg')->size(300)->generate($qrCodeUrl));
            // Log::info($qrCode);
            // return response()->json(['qrcodeurl' => 'test']);
        //, 'secret' => $request->session()->get('two_factor_secret')
        } else {
            return response()->json(['message' => 'Un-Authorized access.'], 401);
        }

        // return view('auth.two-factor-setup', [
        //     'qrCode' => $qrCode,
        //     'secret' => $request->session()->get('two_factor_secret'),
        // ]);
    }
}
