<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KontrakanController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\SewaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AjukanSewaController;


// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
    ->name('dashboard.admin')
    ->middleware(['auth', 'role:admin']);

Route::middleware(['auth', 'role:penyewa'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'penyewa'])->name('dashboard.penyewa');
    Route::post('/ajukan-sewa', [AjukanSewaController::class, 'store'])->name('sewa.ajukan');
});

// CRUD Sewa
Route::resource('sewa', SewaController::class);

// Route khusus approve/reject
Route::post('/sewa/{id}/approve', [SewaController::class, 'approve'])->name('sewa.approve');
Route::post('/sewa/{id}/reject', [SewaController::class, 'reject'])->name('sewa.reject');



Route::get('/pengelola/dashboard', function () {
    return view('dashboard_pengelola');
})->name('dashboard.pengelola')->middleware(['auth', 'role:pengelola']);

// CRUD Kontrakan
Route::resource('kontrakan', KontrakanController::class);

// CRUD Penyewa
Route::resource('penyewa', PenyewaController::class);

// CRUD Sewa
Route::resource('sewa', SewaController::class);