<?php

use App\Http\Controllers\Api\ActivityLogApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\DownloadController;
use App\Http\Controllers\Api\FuzzyApiController;
use App\Http\Controllers\Api\MahasiswaApiController;
use App\Http\Controllers\Api\PembayaranSppApiController;
use App\Http\Controllers\Api\PeminjamanBukuApiController;
use App\Http\Controllers\Api\PengambilanMatakuliahApiController;
use App\Http\Controllers\Api\PrestasiApiController;
use App\Http\Controllers\Api\RoleApiController;
use App\Http\Controllers\Api\UserApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/register', [AuthApiController::class, 'register']);

// Download APK — public (bypasses JS Challenge), with rate limiting
Route::get('/download-apk/{arch}', [DownloadController::class, 'downloadApk'])
    ->middleware(['throttle:10,1']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [AuthApiController::class, 'user']);
    Route::post('/logout', [AuthApiController::class, 'logout']);

    Route::get('/dashboard', [DashboardApiController::class, 'index']);

    Route::apiResource('mahasiswa', MahasiswaApiController::class);
    Route::get('/mahasiswa-search/{nim}', [MahasiswaApiController::class, 'search']);

    Route::get('/prestasi/form-data', [PrestasiApiController::class, 'formData']);
    Route::apiResource('prestasi', PrestasiApiController::class);
    Route::post('/prestasi/{prestasiMahasiswa}/verifikasi', [PrestasiApiController::class, 'verifikasi']);
    Route::get('/prestasi-laporan', [PrestasiApiController::class, 'laporan']);
    Route::post('/fuzzy/hitung', [FuzzyApiController::class, 'hitungUlang']);
    Route::get('/fuzzy/hasil', [FuzzyApiController::class, 'index']);

    Route::get('/pembayaran-spp/form-data', [PembayaranSppApiController::class, 'formData']);
    Route::apiResource('pembayaran-spp', PembayaranSppApiController::class);

    Route::get('/pengambilan-matakuliah/form-data', [PengambilanMatakuliahApiController::class, 'formData']);
    Route::apiResource('pengambilan-matakuliah', PengambilanMatakuliahApiController::class);

    Route::get('/peminjaman-buku/form-data', [PeminjamanBukuApiController::class, 'formData']);
    Route::apiResource('peminjaman-buku', PeminjamanBukuApiController::class);

    Route::middleware('role:operator')->group(function () {
        Route::apiResource('users', UserApiController::class);
        Route::post('/users/{user}/reset-password', [UserApiController::class, 'resetPassword']);
        Route::apiResource('roles', RoleApiController::class);
        Route::get('/permissions', [RoleApiController::class, 'permissions']);
        Route::get('/activity-logs', [ActivityLogApiController::class, 'index']);
        Route::get('/activity-logs-filters', [ActivityLogApiController::class, 'filters']);
    });
});
