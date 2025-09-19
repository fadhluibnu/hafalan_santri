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

Route::prefix('admin-cabang')
    ->name('admin-cabang.')
    ->middleware(['auth', 'admin_cabang'])
    ->group(function () {
        Route::get('/', function () {
            return Inertia::render('AdminCabang/Dashboard');
        })->name('dashboard');
        Route::get('/santri', function () {
            return Inertia::render('AdminCabang/Santri/Index');
        })->name('santri.index');
        Route::get('/santri/create', function () {
            return Inertia::render('AdminCabang/Santri/Create');
        })->name('santri.create');
        Route::get('/santri/{id}/edit', function ($id) {
            return Inertia::render('AdminCabang/Santri/Edit', ['id' => $id]);
        })->name('santri.edit');
        Route::get('/santri/{id}', function ($id) {
            return Inertia::render('AdminCabang/Santri/Show', ['id' => $id]);
        })->name('santri.show');
        Route::delete('/santri/{id}', function ($id) {
            // Dummy delete handler - replace with controller when backend ready
            return back()->with('success', 'Santri deleted (dummy)');
        })->name('santri.destroy');
        Route::get('/guru', function () {
            return Inertia::render('AdminCabang/Guru/Index');
        })->name('guru.index');
        Route::get('/guru/create', function () {
            return Inertia::render('AdminCabang/Guru/Create');
        })->name('guru.create');
        Route::get('/guru/{id}/edit', function ($id) {
            return Inertia::render('AdminCabang/Guru/Edit', ['id' => $id]);
        })->name('guru.edit');
        Route::get('/guru/{id}', function ($id) {
            return Inertia::render('AdminCabang/Guru/Show', ['id' => $id]);
        })->name('guru.show');
        Route::delete('/guru/{id}', function ($id) {
            // Dummy delete handler - replace with controller when backend ready
            return back()->with('success', 'Guru deleted (dummy)');
        })->name('guru.destroy');
        Route::prefix('struktur/kelas')->name('struktur.kelas.')->group(function () {
            Route::get('/', function () {
                return Inertia::render('AdminCabang/Struktur/kelas/Index');
            })->name('index');

            Route::get('/create', function () {
                return Inertia::render('AdminCabang/Struktur/kelas/Create');
            })->name('create');

            Route::get('/{id}', function ($id) {
                // Optionally pass dummy data as props, for now the React page has its own fallback
                return Inertia::render('AdminCabang/Struktur/kelas/Show', [
                    // 'kelas' => ['id' => (int)$id, 'nama' => 'Kelas Tahfidz A', 'tingkat' => 'Juz 30', 'waliKelas' => 'Ustadz Rahman', 'kapasitas' => 25, 'keterangan' => '...']
                ]);
            })->name('show');

            Route::get('/{id}/edit', function ($id) {
                return Inertia::render('AdminCabang/Struktur/kelas/Edit', [
                    // 'kelas' => ['id' => (int)$id, 'nama' => 'Kelas Tahfidz A', 'tingkat' => 'Juz 30', 'waliKelas' => 'Ustadz Rahman', 'kapasitas' => 25, 'keterangan' => '...']
                ]);
            })->name('edit');
            Route::get('/{id}/santri', function ($id) {
                return Inertia::render('AdminCabang/Struktur/kelas/ManageSantri', [
                    // 'kelas' => ['id' => (int)$id, 'nama' => 'Kelas Tahfidz A', 'tingkat' => 'Juz 30', 'waliKelas' => 'Ustadz Rahman', 'kapasitas' => 25, 'keterangan' => '...']
                ]);
            })->name('manage_santri');
            Route::post('/{id}/santri', function ($id) {
                // Expect payload: santri_ids: array
                // For now, just bounce back with a success message
                return back()->with('success', 'Penempatan santri disimpan (dummy)');
            })->name('manage_santri.store');

            Route::delete('/{id}', function ($id) {
                // Dummy destroy kelas
                return back()->with('success', 'Kelas dihapus (dummy)');
            })->name('destroy');
        });
    });
