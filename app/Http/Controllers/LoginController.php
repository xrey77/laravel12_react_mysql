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

        $user = User::where('username', $username)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {

                $credentials = ['username' => $username, 'password' => $password];                
                if (Auth::attempt($credentials)) {
                    // Auth::login($user);

                    $user->tokens()->delete();
                    // $token = $user->createToken('api-token')->plainTextToken;
                    $token = $user->createToken($secret)->plainTextToken;       

                    // $qrCode = base64_encode($user->twoFactorQrCodeSvg());
                    // $qrCode = $user->twoFactorQrCodeSvg();
                    // $imageData = file_get_contents($qrCode);
                    // $base64EncodedImage = base64_encode($qrCode);
                    // $url = $user->twoFactorQrCodeUrl();
                    // $qrCode = QrCode::format('png')->size(200)->generate($url);

                    // return response()->json($qrCode);

                    // $recovery_code = encrypt($user->two_factor_recovery_codes);
                    // $base64ImageWithPrefix = "data:" . "image/jpeg" . ";base64," . $qrCode;



                    // $twoFactorQrCodeUrl = $google2fa->getQrCodeUrl(
                    //     decrypt($secret) // The secret, which is encrypted
                    // );
                    // return response()->json($twoFactorQrCodeUrl);
                    // $url = base_64($user->twoFactorQrCodeUrl());
                    // $user->qrcodeurl = $user->twoFactorQrCodeUrl();
                    // $user->qrcodeurl = base64_encode($user->twoFactorQrCodeSvg());
                    // return response()->json($secret);
                    // $crftoken = $request->session()->token();
                    // $crftoken = csrf_token();
                    // return response()->json($crftoken);
                    return response()->json([                    
                        'statuscode' => '201',
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
                    ]);

                }



        
            } else {
                return response()->json(['statuscode' => 404, 'message' => 'Invalid password, try again.']);
            }
        } else {
            return response()->json(['statuscode' => 404, 'message' => 'Username not found, please register first.']);
        }
    }

    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }    

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    } 
    // default 60 minutes
    // 1 month = 60000 minutes
    // 1 day = 1440
    public function getAuthenticatedUser()
    {
            try {
                    if (! $user = JWTAuth::parseToken()->authenticate()) {
                            return response()->json(['user_not_found'], 404);
                    }

            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                    return response()->json(['token_expired'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    return response()->json(['token_invalid'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                    return response()->json(['token_absent'], $e->getStatusCode());

            }
            return response()->json(compact('user'));
    }


}
