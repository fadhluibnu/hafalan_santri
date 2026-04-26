<?php

use App\Http\Controllers\AdminCabang\SantriController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdmin\AdminCabangController;
use App\Http\Controllers\SuperAdmin\PondokController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
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

        // Ujian monitor (read-only)
        Route::get('ujian', [\App\Http\Controllers\SuperAdmin\UjianController::class, 'index'])->name('ujian.index');
        Route::get('ujian/{id}', [\App\Http\Controllers\SuperAdmin\UjianController::class, 'show'])->name('ujian.show');

        // Laporan
        Route::get('laporan', [ReportController::class, 'daftarSantri'])->name('laporan');
        Route::get('laporan/daftar-santri', [ReportController::class, 'daftarSantri'])->name('laporan.daftar-santri');
        Route::get('laporan/cetak-santri', [ReportController::class, 'cetakSantri'])->name('laporan.cetak-santri');
        Route::get('laporan/cetak-santri/{nis}/pdf', [ReportController::class, 'cetakSantriPdf'])->name('laporan.cetak-santri.pdf');
        Route::get('laporan/ujian', [ReportController::class, 'laporanUjian'])->name('laporan.ujian');
        Route::get('laporan/ujian/{id}', [ReportController::class, 'laporanUjianDetail'])->name('laporan.ujian.detail');
        Route::get('laporan/ujian/{id}/pdf', [ReportController::class, 'laporanUjianPdf'])->name('laporan.ujian.pdf');
        Route::get('laporan/raport', [ReportController::class, 'raport'])->name('laporan.raport');
        Route::get('laporan/raport/{nis}/pdf', [ReportController::class, 'raportPdf'])->name('laporan.raport.pdf');
    });

Route::prefix('admin-cabang')
    ->name('admin-cabang.')
    ->middleware(['auth', 'admin_cabang'])
    ->group(function () {
        // Gunakan controller agar data dinamis dari DashboardController@index tersedia di view
        Route::get('/', [\App\Http\Controllers\AdminCabang\DashboardController::class, 'index'])->name('dashboard');
        
        // Set tahun ajaran session
        Route::post('/set-tahun-ajaran', [\App\Http\Controllers\TahunAjaranSessionController::class, 'setSelected'])->name('set-tahun-ajaran');

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

        // Tahun Ajaran Routes
        Route::resource('tahun-ajaran', \App\Http\Controllers\AdminCabang\TahunAjaranController::class);
        Route::post('tahun-ajaran/{id}/set-active', [\App\Http\Controllers\AdminCabang\TahunAjaranController::class, 'setActive'])->name('tahun-ajaran.set-active');
        Route::post('tahun-ajaran/{id}/close', [\App\Http\Controllers\AdminCabang\TahunAjaranController::class, 'close'])->name('tahun-ajaran.close');

        // Skema Penilaian Dinamis
        Route::get('skema-penilaian', [\App\Http\Controllers\AdminCabang\SkemaPenilaianController::class, 'edit'])->name('skema-penilaian.edit');
        Route::put('skema-penilaian', [\App\Http\Controllers\AdminCabang\SkemaPenilaianController::class, 'update'])->name('skema-penilaian.update');

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

        // Ujian monitor (read-only)
        Route::get('ujian', [\App\Http\Controllers\AdminCabang\UjianController::class, 'index'])->name('ujian.index');
        Route::get('ujian/{id}', [\App\Http\Controllers\AdminCabang\UjianController::class, 'show'])->name('ujian.show');

        // Laporan
        Route::get('laporan', [ReportController::class, 'daftarSantri'])->name('laporan');
        Route::get('laporan/daftar-santri', [ReportController::class, 'daftarSantri'])->name('laporan.daftar-santri');
        Route::get('laporan/cetak-santri', [ReportController::class, 'cetakSantri'])->name('laporan.cetak-santri');
        Route::get('laporan/cetak-santri/{nis}/pdf', [ReportController::class, 'cetakSantriPdf'])->name('laporan.cetak-santri.pdf');
        Route::get('laporan/ujian', [ReportController::class, 'laporanUjian'])->name('laporan.ujian');
        Route::get('laporan/ujian/{id}', [ReportController::class, 'laporanUjianDetail'])->name('laporan.ujian.detail');
        Route::get('laporan/ujian/{id}/pdf', [ReportController::class, 'laporanUjianPdf'])->name('laporan.ujian.pdf');
        Route::get('laporan/raport', [ReportController::class, 'raport'])->name('laporan.raport');
        Route::get('laporan/raport/{nis}/pdf', [ReportController::class, 'raportPdf'])->name('laporan.raport.pdf');
    });

// Ustadz Routes (menggantikan Guru Routes)
Route::prefix('ustadz')
    ->name('ustadz.')
    ->middleware(['auth', 'ustadz'])
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Ustadz\DashboardController::class, 'index'])->name('dashboard');

        // gunakan resource controller agar route names standard tersedia (index, create, store, show, edit, update, destroy)
        Route::resource('/hafalan', \App\Http\Controllers\Ustadz\HafalanController::class);

        // Ujian (ustadz full access)
        Route::resource('/ujian', \App\Http\Controllers\Ustadz\UjianController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::put('/ujian/{id}/nilai', [\App\Http\Controllers\Ustadz\UjianController::class, 'updateNilai'])->name('ujian.nilai.update');
        Route::patch('/ujian/{id}/status', [\App\Http\Controllers\Ustadz\UjianController::class, 'updateStatus'])->name('ujian.status.update');

        // Santri routes (read-only untuk ustadz)
        Route::get('/santri', [\App\Http\Controllers\Ustadz\SantriController::class, 'index'])->name('santri.index');
        Route::get('/santri/{nis}', [\App\Http\Controllers\Ustadz\SantriController::class, 'show'])->name('santri.show');
        Route::get('/santri/{nis}/hafalan', [\App\Http\Controllers\Ustadz\SantriController::class, 'hafalan'])->name('santri.hafalan');
        Route::get('/santri/{nis}/hafalan/pdf', [\App\Http\Controllers\Ustadz\SantriController::class, 'exportPdf'])->name('santri.hafalan.pdf');

        // Laporan
        Route::get('/laporan', [ReportController::class, 'daftarSantri'])->name('laporan');
        Route::get('/laporan/daftar-santri', [ReportController::class, 'daftarSantri'])->name('laporan.daftar-santri');
        Route::get('/laporan/cetak-santri', [ReportController::class, 'cetakSantri'])->name('laporan.cetak-santri');
        Route::get('/laporan/cetak-santri/{nis}/pdf', [ReportController::class, 'cetakSantriPdf'])->name('laporan.cetak-santri.pdf');
        Route::get('/laporan/ujian', [ReportController::class, 'laporanUjian'])->name('laporan.ujian');
        Route::get('/laporan/ujian/{id}', [ReportController::class, 'laporanUjianDetail'])->name('laporan.ujian.detail');
        Route::get('/laporan/ujian/{id}/pdf', [ReportController::class, 'laporanUjianPdf'])->name('laporan.ujian.pdf');
        Route::get('/laporan/raport', [ReportController::class, 'raport'])->name('laporan.raport');
        Route::get('/laporan/raport/{nis}/pdf', [ReportController::class, 'raportPdf'])->name('laporan.raport.pdf');
    });
