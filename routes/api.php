<?php

use App\Http\Controllers\Controler;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\MfaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\ChartController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/uploadpicture', [UserController::class, 'updateProfilepicture']);
Route::patch('/otpvalidation', [UserController::class, 'validateOtp']);
Route::post('/addproduct', [ProductsController::class, 'addProduct']);
Route::patch('/changepassword', [MfaController::class, 'changePassword']);
Route::patch('/enablemfa', [MfaController::class, 'enableMfa']);
Route::patch('/disablemfa', [MfaController::class, 'disableMfa']);
Route::patch('/updateuser/{id}', [UserController::class, 'updateUser']);
Route::delete('/deleteuser/{id}', [UserController::class, 'deleteUser']);
Route::patch('/changeuserpassword/{id}', [UserController::class, 'changeUserpassword']);
Route::patch('/enablemfa/{id}', [UserController::class, 'enableMfa']);
Route::get('/getallusers', [UserController::class, 'getAllusers']);
Route::get('/getuserid/{id}', [UserController::class, 'getUserbydid']);
Route::get('/productlist/{page}', [ProductsController::class, 'listProducts']);
Route::get('/productsearch/{key}', [ProductsController::class, 'productSearch']);
Route::get('/chartdata', [ChartController::class, 'generateChart']);

