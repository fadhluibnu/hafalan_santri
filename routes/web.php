<?php

use App\Http\Controllers\AdminCabang\SantriController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdmin\AdminCabangController;
use App\Http\Controllers\SuperAdmin\PondokController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AdminCabang\KelasController;

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
        // Gunakan controller agar data dinamis dari DashboardController@index tersedia di view
        Route::get('/', [\App\Http\Controllers\AdminCabang\DashboardController::class, 'index'])->name('dashboard');

        // Santri Excel export/import routes (harus sebelum resource route)
        Route::get('santri/export', [SantriController::class, 'export'])->name('santri.export');
        Route::get('santri/template', [SantriController::class, 'downloadTemplate'])->name('santri.template');
        Route::post('santri/import', [SantriController::class, 'import'])->name('santri.import');
        
        // Santri PDF export route
        Route::get('santri/{nis}/pdf', [\App\Http\Controllers\AdminCabang\SantriPdfController::class, 'generatePdf'])->name('santri.pdf');
        
        // Santri Hafalan Progress routes
        Route::get('santri/{nis}/hafalan', [\App\Http\Controllers\AdminCabang\SantriHafalanController::class, 'index'])->name('santri.hafalan');
        Route::get('santri/{nis}/hafalan/pdf', [\App\Http\Controllers\AdminCabang\SantriHafalanController::class, 'exportPdf'])->name('santri.hafalan.pdf');

        // Santri Resource Route
        Route::resource('santri', SantriController::class);

        // Ustadz Routes (menggantikan Guru)
        Route::resource('ustadz', \App\Http\Controllers\AdminCabang\UstadzController::class);

        // Ganti blok closure sebelumnya dengan resource controller untuk struktur/kelas
        Route::resource('struktur/kelas', KelasController::class)
            ->names([
                'index' => 'struktur.kelas.index',
                'create' => 'struktur.kelas.create',
                'store' => 'struktur.kelas.store',
                'show' => 'struktur.kelas.show',
                'edit' => 'struktur.kelas.edit',
                'update' => 'struktur.kelas.update',
                'destroy' => 'struktur.kelas.destroy',
            ]);

        // Tambahkan route untuk manage santri (penempatan)
        Route::get('struktur/kelas/{id}/santri', [KelasController::class, 'manageSantri'])
            ->name('struktur.kelas.manage_santri');
        Route::post('struktur/kelas/{id}/santri', [KelasController::class, 'storeSanTri'])
            ->name('struktur.kelas.manage_santri.store');
    });

// Ustadz Routes (menggantikan Guru Routes)
Route::prefix('ustadz')
    ->name('ustadz.')
    ->middleware(['auth', 'ustadz'])
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Ustadz\DashboardController::class, 'index'])->name('dashboard');

        // gunakan resource controller agar route names standard tersedia (index, create, store, show, edit, update, destroy)
        Route::resource('/hafalan', \App\Http\Controllers\Ustadz\HafalanController::class);

        // Santri routes (read-only untuk ustadz)
        Route::get('/santri', [\App\Http\Controllers\Ustadz\SantriController::class, 'index'])->name('santri.index');
        Route::get('/santri/{nis}', [\App\Http\Controllers\Ustadz\SantriController::class, 'show'])->name('santri.show');
        Route::get('/santri/{nis}/hafalan', [\App\Http\Controllers\Ustadz\SantriController::class, 'hafalan'])->name('santri.hafalan');
        Route::get('/santri/{nis}/hafalan/pdf', [\App\Http\Controllers\Ustadz\SantriController::class, 'exportPdf'])->name('santri.hafalan.pdf');

        // Laporan - gunakan controller agar dinamis
        Route::get('/laporan', [\App\Http\Controllers\Ustadz\LaporanController::class, 'index'])->name('laporan');
    });

