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
use OpenApi\Attributes as OA;


class UserController extends Controller
{

    #[OA\Post(
        path: '/api/logout',
        operationId: 'userLogout',
        summary: 'Logout current user',
        description: 'Deletes the current access token',
        security: [['bearerAuth' => []]],
        tags: ['Authentication']
    )]
    #[OA\Response(
        response: 200,
        description: 'Logged out successfully.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully.')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully.',
        ],200);
    }

    #[OA\Get(
        path: '/api/getuserid/{id}',
        summary: 'Retrieve by user id',
        security: [['sanctum' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of the user to retrieve',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'User Authenticated Successfully'),
            new OA\Response(response: 401, description: 'Un-Authorized Access')
        ]
    )]      
    public function getUserbydid(string $id) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            return response()->json(['message' => 'User Authenticated Successfully.','user' => $user], 200);

        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }

    #[OA\Get(
        path: '/api/getallusers',
        summary: 'Get all users',
        security: [['sanctum' => []]],
        tags: ['Users'],
    )]
    #[OA\Response(response: 200, description: 'User Authenticated Successfully')]
    #[OA\Response(response: 401, description: 'Un-Authorized Access')]
    #[OA\Response(response: 404, description: 'Users is empty')]
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

    #[OA\Put(
        path: '/api/profileupdate/{id}',
        summary: 'Update user details',
        security: [['sanctum' => []]],
        tags: ['Users'],
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'firstname', type: 'string'),
                new OA\Property(property: 'lastname', type: 'string'),
                new OA\Property(property: 'mobile', type: 'string'),
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Profile updated successfully')]
    #[OA\Response(response: 401, description: 'Un-Authorized Access')]
    #[OA\Response(response: 404, description: 'User not found')]
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

    #[OA\Delete(
        path: '/api/deleteuser/{id}',
        summary: 'Delete a user',
        security: [['sanctum' => []]],
        tags: ['Users'],
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'User Deleted successfully')]
    #[OA\Response(response: 401, description: 'Un-Authorized Access')]    
    public function deleteUser(int $id) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'User Deleted successfully.'],200);
        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }

    #[OA\Post(
        path: '/api/changepassword/{id}',
        summary: 'Change user password',
        security: [['sanctum' => []]],
        tags: ['Users'],
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['password'],
            properties: [
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'newpassword123')
            ]
        )
    )]
    #[OA\Response(
        response: 200, 
        description: 'Success', 
        content: new OA\JsonContent(properties: [new OA\Property(property: 'message', type: 'string')])
    )]
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

    #[OA\Post(
        path: '/api/uploadpicture/{id}',
        summary: 'Update profile picture',
        tags: ['Users'],        
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['id', 'profilepic'],
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'profilepic', type: 'string', format: 'binary')
                ]
            )
        )
    )]
    #[OA\Response(response: 200, description: 'Picture uploaded')]
    #[OA\Response(response: 404, description: 'User or Image not found')]
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

    // MFA Toggle
    #[OA\Post(
        path: '/api/mfa/activate/{id}',
        summary: 'Enable/Disable MFA', 
        security: [['sanctum' => []]], 
        tags: ["Authentication"]
        )]
    #[OA\RequestBody(content: new OA\JsonContent(properties: [new OA\Property(property: 'Twofactorenabled', type: 'boolean', example: true)]))]
    #[OA\Response(
        response: 200, 
        description: 'MFA Status Updated',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'qrcodeurl', type: 'string', nullable: true)
        ])
    )]
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

    // Validate OTP
    #[OA\Post(
        path: '/api/mfa/verifytotp/{id}',
        summary: 'Validate OTP code',
        tags: ["Authentication"]
        )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['id', 'otp'],
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'otp', type: 'string', example: '123456')
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'OTP Validated')]
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