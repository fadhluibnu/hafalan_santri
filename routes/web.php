<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Super Admin Routes
Route::prefix('super-admin')
    ->name('super-admin.')
    ->middleware(['auth', 'super_admin'])
    ->group(function () {
        Route::get('/', function () {
            return Inertia::render('SuperAdmin/Dashboard');
        })->name('dashboard');

        // Pondok Routes
        Route::resource('pondok', \App\Http\Controllers\SuperAdmin\PondokController::class);

        // AdminCabang Routes
        Route::resource('admin-cabang', \App\Http\Controllers\SuperAdmin\AdminCabangController::class);
    });
