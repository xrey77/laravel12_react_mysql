<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SigninController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\PdfController;

Route::get('/', function () {
    return inertia('Home');
});

Route::get('/about', function () {
    return inertia('About');
});

Route::get('/products/report', function () {
    return inertia('Productreport');
});

Route::get('/chart', function () {
    return inertia('Chart');
});


Route::get('/contact', function () {
    return inertia('Contact');
});

Route::get('/profile', function () {
    return inertia('Profile');
});

Route::get('/productlist', function () {
    return inertia('Prodlist');
});

Route::get('/productcatalog', function () {
    return inertia('Prodcatalog');
});

Route::get('/productsearch', function () {
    return inertia('Prodsearch');
});

Route::get('/pdfreport', function () {
    return inertia('Pdfreport');
});

Route::get('/saleschart', function () {
    return inertia('Saleschart');
});


Route::get('/products/report', [PdfController::class, 'generatePdf']);

