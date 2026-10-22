<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Redirect homepage to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Authenticated & CRUD Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::put('/user/{id}', [AuthController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [AuthController::class, 'destroy'])->name('user.delete');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});