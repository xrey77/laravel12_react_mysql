<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class GenerateTwoFactorQrCode
{
    public function __invoke(Request $request, Google2FA $google2fa)
    {
        $secret = $request->session()->has('two_factor_secret')
            ? $request->session()->get('two_factor_secret')
            : $google2fa->generateSecretKey();

        // Store the secret in the session
        $request->session()->put('two_factor_secret', $secret);
        $request->session()->put('two_factor_verified', false);

        return $google2fa->getQRCodeUrl(
            config('app.name'),
            'username@example.com', // Replace with a unique identifier if needed
            $secret
        );
    }
}
