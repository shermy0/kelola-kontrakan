<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KontrakanController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\SewaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
    ->name('dashboard.admin')
    ->middleware(['auth', 'role:admin']);

Route::get('/pengelola/dashboard', function () {
    return view('dashboard_pengelola');
})->name('dashboard.pengelola')->middleware(['auth', 'role:pengelola']);

// CRUD Kontrakan
Route::resource('kontrakan', KontrakanController::class);

// CRUD Penyewa
Route::resource('penyewa', PenyewaController::class);

// CRUD Sewa
Route::resource('sewa', SewaController::class);