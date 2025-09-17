<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdmin\AdminCabangController;
use App\Http\Controllers\SuperAdmin\PondokController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::middleware(['guest'])->group(function () {

//     Route::post('/login', [LoginController::class, 'login']);

// });

// Route::prefix('/super-admin')->as('super_admin')->middleware(['auth:sanctum', 'super_admin'])->group(function() {

//     Route::resource('/pondok', PondokController::class);
//     // ->except(['update']);

//     Route::resource('/admin_cabang', AdminCabangController::class);

// });