<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SigninController;
use App\Http\Controllers\TwoFactorController;

Route::get('/', function () {
    return inertia('Home');
});

Route::get('/about', function () {
    return inertia('About');
});

Route::get('/contact', function () {
    return inertia('Contact');
});

Route::get('/profile', function () {
    return inertia('Profile');
});


Route::middleware('auth')->group(function () {
    Route::get('/2fa/enable', [TwoFactorController::class, 'showEnableForm'])->name('2fa.enable');
    Route::post('/2fa/confirm', [TwoFactorController::class, 'confirmEnable'])->name('2fa.confirm');
});
