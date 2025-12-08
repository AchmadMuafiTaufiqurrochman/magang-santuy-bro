<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\CustomRegisterController;

Route::get('/', function () {
    return view('welcome');
});

// Custom login routes
Route::get('/app/login', [CustomLoginController::class, 'showLoginForm'])->name('login');
Route::post('/app/login', [CustomLoginController::class, 'login']);
Route::post('/app/logout', [CustomLoginController::class, 'logout'])->name('logout');

// Registration routes (if needed)

Route::get('/app/register', [CustomRegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/app/register', [CustomRegisterController::class, 'register']);
// Other routes...


