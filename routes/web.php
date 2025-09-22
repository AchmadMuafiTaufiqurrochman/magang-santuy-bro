<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomLoginController;

Route::get('/', function () {
    return view('welcome');
});

// Custom login routes
Route::get('/app/login', [CustomLoginController::class, 'showLoginForm'])->name('login');
Route::post('/app/login', [CustomLoginController::class, 'login']);
Route::post('/app/logout', [CustomLoginController::class, 'logout'])->name('logout');
