<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
// use PragmaRX\Google2FALaravel\Facade as Google2FA;
// use PragmaRX\Google2FA\Google2FA;
use Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Exception;
use Image;

class UserController extends Controller
{

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully.',
        ],200);
    }

    public function getUserbydid(string $id) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            return response()->json(['message' => 'User Authenticated Successfully.','user' => $user], 200);

        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }

    public function getAllusers() {
        if (Auth::guard('sanctum')->check()) {
            $users = User::all();
            if ($users->count() == 0) {
                return response()->json(['message' => 'Users is empty.'],404);
            }
            return response()->json(['message' => 'User Authenticated Successfully.', 'user' => $users],200);
        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }

    public function updateUser(string $id, Request $request) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found...'],404);
            }
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->mobile = $request->mobile;
            $user->save();
            return response()->json(['message' => 'Profile updated sucessfully...'],200);
        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }

    public function deleteUser(int $id) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'User Deleted successfully.'],200);
        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }

    public function changeUserpassword(int $id, Request $request)
    {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json(['message' => 'You change your password successfully....'],200);
        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }

    public function updateProfilepicture(Request $request) 
    {
        $userid = $request->id;
        $user = User::find($userid);
        if (!$user) {
            return response()->json(['message' => 'User not found...'],404);
        }
        // GET MULTIPART FORM FILE
        if ($request->hasFile('profilepic')) {
            $file = $request->file('profilepic');
            $img = $file->getClientOriginalName();
            // ASSIGN NEW FILENAME
            $ext = $request->file('profilepic')->guessExtension(); 
            $newfile = '00' . $userid . '.' . $ext;
            // SAVE NEW IMAGE FILE TO users folder in storage folder // $file->storeAs('users', $newfile);
            // SAVE NEW IMAGE to users folder in public folder // $file->move('users', $newfile);
            // RESISE NEW IMAGE
            // $destinationPath = public_path('/users'); $imgFile = Image::read($file->getRealPath());

            $img = Image::read($file->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('users/' . $newfile));
        
            // Store the original image
            $file->move(public_path('users'), $newfile);
            
            $user = User::find($userid);
            if($user) {
                $user->profilepic = "http://127.0.0.1:8000/users/" . $newfile;
                $user->save();
            }    
            return response()->json(['message' => 'New picture has been uploaded successfully.'],200);
        } else {
            return response()->json(['message' => 'Image not found.'],404);
        }

    }



    public function enableMfa($id, Request $request) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found...'],404);
            }
            $isEnabled = $request->Twofactorenabled;
            if ($isEnabled) {

                $issuer = config('services.issuer_service.key');
                $google2fa = new Google2FA();
                $secretKey = $google2fa->generateSecretKey();
                // Log::Debug("SECRET KEY :", encrypt($secretKey));
                $userEmail = $user->email;
                $companyName = $issuer;
                $qrCodeUrl = $google2fa->getQRCodeUrl(
                    $companyName,
                    $userEmail,
                    $secretKey
                );
        
                // Configure the PNG renderer for BaconQrCode
                $renderer = new ImageRenderer(
                    new RendererStyle(400), // Set the size
                    new ImagickImageBackEnd()
                );
                $writer = new Writer($renderer);
                
                // Write the QR code as a PNG image string
                $qrcode_image_string = $writer->writeString($qrCodeUrl);
        
                // Encode the image string to base64 for embedding in the view
                $qrcode_base64 = base64_encode($qrcode_image_string);

                $qrcode = 'data:image/svg+xml;base64,' . $qrcode_base64;
                $user->google2fa_secret = encrypt($secretKey);
                $user->qrcodeurl = $qrcode;
                $user->save();
                return response()->json(['message' => 'Multi-Factor Authenticator Enabled successfully, please scan QRCODE using your Google Authenticator from your Mobile Phone!', 'qrcodeurl' => $qrcode],200);
            } else {
                $user->qrcodeurl = null;
                $user->save();
                return response()->json(['message' => 'Multi-Factor Authenticator Disabled successfully.', 'qrcodeurl' => null],200);
            }

        } else {
            return response()->json(['message' => 'Un-Authorized access.'], 401);
        }
    }

    public function validateOtp(Request $request) {
            $user = User::find($request->id);
            if ($user) {
              try {
                $secret = decrypt($user->google2fa_secret);
                $otp = $request->otp;
                if (Google2FA::verifyKey($secret, $otp)) {
                    // Google2FA::login();
                    return response()->json(['message' => 'OTP Code is successfully validated.','username' => $user->username],200);
                } else {
                    return response()->json(['message' => 'Invalid OTP code, please try again.'], 404);
                }
              } catch(\Exception $e) {
                return response()->json(['message' => $e->getMessage()]);
              }
            } else {
                return response()->json(['message' => 'Un-Authorized access.'], 401);
            }

    }

}