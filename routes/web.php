<?php

use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get');

// Super Admin Routes
Route::prefix('super-admin')
    ->name('super-admin.')
    ->middleware(['auth', 'super_admin'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Pondok Routes
        Route::resource('pondok', \App\Http\Controllers\SuperAdmin\PondokController::class);

        // AdminCabang Routes
        Route::resource('admin-cabang', \App\Http\Controllers\SuperAdmin\AdminCabangController::class);
    });
