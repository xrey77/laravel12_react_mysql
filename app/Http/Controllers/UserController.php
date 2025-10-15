<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PragmaRX\Google2FALaravel\Facade as Google2FA;
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
        ]);
    }

    public function getUserbydid(string $id) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            return response()->json(['statuscode' => 200, 'message' => 'User Authenticated Successfully.','user' => $user]);

        } else {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
    }

    public function getAllusers() {
        if (Auth::guard('sanctum')->check()) {
            $users = User::all();
            if ($users->count() == 0) {
                return response()->json(['statuscode' => 404, 'message' => 'Users is empty.']);
            }
            return response()->json(['statuscode'=> 200,'message' => 'User Authenticated Successfully.', 'user' => $users]);
        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }

    public function updateUser(string $id, Request $request) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['statuscode' => 404, 'message' => 'User not found...']);
            }
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->mobile = $request->mobile;
            $user->save();
            return response()->json(['statuscode' => 200, 'message' => 'Profile updated sucessfully...']);                
        } else {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
    }

    public function deleteUser(int $id) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['statuscode' => 200, 'message' => 'User Deleted successfully.']);
        } else {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
    }

    public function changeUserpassword(int $id, Request $request)
    {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json(['statuscode' => 200, 'message' => 'You change your password successfully....']);    
        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }

    public function updateProfilepicture(Request $request) 
    {
        $userid = $request->id;
        $user = User::find($userid);
        if (!$user) {
            return response()->json(['statuscode' => 404, 'message' => 'User not found...']);
        }
        // uploadedImage = $request->file('image');
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
            return response()->json(['statuscode' => 200, 'message' => 'New picture has been uploaded successfully.']);
        } else {
            return response()->json(['statuscode' => 404, 'message' => 'Image not found.']);
        }

    }

    public function getCrftoken($id, Request $request) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            if ($user) {
                // $token = encrypt(csrf_token());
                // Log::debug('x-csrf-token :', $token);    
                // $token = encrypt($request->session()->token());
                // $token1 = $request->session()->token();
                $token = encrypt(csrf_token());

                return response()->json(['statuscode' => 200, 'message' => 'Success..', 'csrtoken' => $token]);
            } else {
                return response()->json(['statuscode' => 404, 'message' => 'User not found..']);
                
            }
            // Log::debug('x-csrf-token :', $token);
            // $userQr = auth()->user();
        } else {
            return response()->json(['message' => 'Un-Authorized access.'], 401);
        }
    }

    public function enableMfa($id, Request $request) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['statuscode' => 404, 'message' => 'User not found...']);
            }
            $isEnabled = $request->Twofactorenabled;
            if ($isEnabled) {

                $issuer = config('services.issuer_service.key');
                $google2fa = app(Google2FA::class);
                $secretKey = Google2FA::generateSecretKey();
                // $secretkey = Google2FA::generateSecretKey();
                $userEmail = $user->email;
                $companyName = $issuer;
                $qrCodeUrl = Google2FA::getQRCodeUrl(
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
                $user->google2fa_secret = $secretKey;
                $user->qrcodeurl = $qrcode;
                // $qrcode = $user->two_factor_recovery_codes;
                $user->save();
                return response()->json(['statuscode' => 200, 'message' => 'Multi-Factor Authenticator Enabled successfully, please scan QRCODE using your Google Authenticator from your Mobile Phone!', 'qrcodeurl' => $qrcode]);
            } else {
                $user->qrcodeurl = null;
                $user->save();
                return response()->json(['statuscode' => 200, 'message' => 'Multi-Factor Authenticator Disabled successfully.', 'qrcodeurl' => null]);    
            }

        } else {
            return response()->json(['message' => 'Un-Authorized access.'], 401);
        }
    }

    public function validateMfa(Request $request) {
        if (Auth::guard('sanctum')->check()) {
            if (Google2FA::verifyGoogle2FA(request('one_time_password'), $user->google2fa_secret)) {
                return response()->json(['statuscode' => 200, 'message' => 'OTP Code is successfully validated.']);    
            } else {
                return response()->json(['message' => 'Invalid OTP code, please try again.'], 404);
            }
        } else {
            return response()->json(['message' => 'Un-Authorized access.'], 401);
        }

    }

}