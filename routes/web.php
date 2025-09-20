<?php

use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdmin\AdminCabangController;
use App\Http\Controllers\SuperAdmin\PondokController;
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
        Route::resource('pondok', PondokController::class);

        // AdminCabang Routes
        Route::resource('admin-cabang', AdminCabangController::class);
    });

    // Guru Routes (UI only, using Inertia pages)
    Route::prefix('guru')
        ->name('guru.')
        // ->middleware(['auth', 'guru'])
        ->group(function () {
            Route::get('/', function () {
                return Inertia::render('Guru/Dashboard');
            })->name('dashboard');

            Route::prefix('hafalan')->name('hafalan.')->group(function () {
                Route::get('/', function () {
                    return Inertia::render('Guru/Hafalan/Index');
                })->name('index');

                Route::get('/create', function () {
                    return Inertia::render('Guru/Hafalan/Create');
                })->name('create');

                Route::get('/{id}', function ($id) {
                    return Inertia::render('Guru/Hafalan/Show', ['id' => $id]);
                })->name('show');

                Route::get('/{id}/edit', function ($id) {
                    return Inertia::render('Guru/Hafalan/Edit', ['id' => $id]);
                })->name('edit');
            });

            // Laporan
            Route::get('/laporan', function () {
                return Inertia::render('Guru/Laporan');
            })->name('laporan');
        });
