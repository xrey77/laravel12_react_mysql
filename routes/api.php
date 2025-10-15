<?php

use App\Http\Controllers\Controler;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\MfaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// Route::get('/enablemfa', [TwoFactorController::class, 'show']);

// Route::middleware(['auth:sanctum', 'two-factor-enabled'])->group(function () {

    // Route::get('/user/two-factor-qr-code', function (Request $request) {
    //     if (Auth::guard('sanctum')->check()) {

    //         $qrCode = base64_encode(
    //                 QrCode::format('png')
    //                     ->size(200)
    //                     ->generate($user->twoFactorQrCodeUrl())
    //             );
    //         return response()->json(['qrcodeurl' => $qrCode]);
    //     } else {
    //         return response()->json(['message' => 'Un-Authorized access.'], 401);
    //     }

    // });
// });


// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/user', function (Request $request) {
//         return $request->user(); // Returns the authenticated user model
//     });    

//     // Route::post('/logout', [AuthController::class, 'logout']);
// });


Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
// Route::post('/mfa', [MfaController::class, 'mfalogin']);
Route::patch('/changepassword', [MfaController::class, 'changePassword']);
Route::patch('/enablemfa', [MfaController::class, 'enableMfa']);
Route::patch('/disablemfa', [MfaController::class, 'disableMfa']);
Route::get('/getallusers', [UserController::class, 'getAllusers']);
Route::get('/getuserid/{id}', [UserController::class, 'getUserbydid']);
Route::patch('/updateuser/{id}', [UserController::class, 'updateUser']);
Route::delete('/deleteuser/{id}', [UserController::class, 'deleteUser']);
Route::patch('/changeuserpassword/{id}', [UserController::class, 'changeUserpassword']);
Route::post('/uploadpicture', [UserController::class, 'updateProfilepicture']);
Route::patch('/enablemfa/{id}', [UserController::class, 'enableMfa']);
//->middleware(['auth', '2fa']);
// Route::get('/crftoken/{id}', [UserController::class, 'getCrftoken'])->withoutMiddleware([VerifyCsrfToken::class]);
// Route::post('/user/two-factor-authentication', function () {
//     // ...
// })->withoutMiddleware([VerifyCsrfToken::class]);


// Route::get('/crftoken', function (Request $request) {
//     $token = encrypt($request->session()->token());
//     return $token;
// });
