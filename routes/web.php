<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PembayaranSppController;
use App\Http\Controllers\PeminjamanBukuController;
use App\Http\Controllers\PrestasiMahasiswaController;
use App\Http\Controllers\PengambilanMatakuliahController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\FuzzyPrestasiController;
use Illuminate\Support\Facades\Auth;
use App\Models\JenisPrestasi;
use App\Models\Mahasiswa;
use App\Models\PrestasiMahasiswa;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : view('auth.login');
});

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.process');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

Route::get('dashboard', function () {
    $statusVerifikasi = [
        PrestasiMahasiswa::where('status_verifikasi', 'menunggu')->count(),
        PrestasiMahasiswa::where('status_verifikasi', 'diterima')->count(),
        PrestasiMahasiswa::where('status_verifikasi', 'ditolak')->count(),
    ];

    $prestasiPerJenis = PrestasiMahasiswa::join('jenis_prestasi', 'prestasi.id_jenis', '=', 'jenis_prestasi.id_jenis')
        ->selectRaw('jenis_prestasi.nama_jenis, COUNT(*) as total')
        ->groupBy('jenis_prestasi.nama_jenis')
        ->pluck('total', 'nama_jenis')
        ->toArray();

    $prestasiPerTingkat = PrestasiMahasiswa::join('tingkat_prestasi', 'prestasi.id_tingkat', '=', 'tingkat_prestasi.id_tingkat')
        ->selectRaw('tingkat_prestasi.nama_tingkat, COUNT(*) as total')
        ->groupBy('tingkat_prestasi.nama_tingkat')
        ->pluck('total', 'nama_tingkat')
        ->toArray();

    $trenBulanan = PrestasiMahasiswa::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan, COUNT(*) as total")
        ->whereNotNull('tanggal')
        ->where('tanggal', '>=', now()->subMonths(6))
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->pluck('total', 'bulan')
        ->toArray();

    return view('dashboard', [
        'jumlahMahasiswa'   => Mahasiswa::count(),
        'jumlahReferensi'   => JenisPrestasi::count(),
        'jumlahPendaftaran' => PrestasiMahasiswa::count(),
        'jumlahCapaian'     => PrestasiMahasiswa::where('status_verifikasi', 'diterima')->count(),
        'statusVerifikasi'  => $statusVerifikasi,
        'prestasiPerJenis'  => $prestasiPerJenis,
        'prestasiPerTingkat'=> $prestasiPerTingkat,
        'trenBulanan'       => $trenBulanan,
    ]);
})->name('dashboard');

Route::get('info-terkait', function () {
    return view('info-terkait');
})->name('info-terkait');

Route::get('/halaman-1', function () {
    return view('halaman-1');
});

Route::get('/halaman-2', function () {
    return view('halaman-2');
});

Route::get('/halaman-3', function () {
    return view('halaman-3');
});

Route::get('data-mahasiswa', [MahasiswaController::class, 'index'])
    ->middleware('permission:mahasiswa.view')
    ->name('data-mahasiswa');

Route::get('pembayaran-spp', [PembayaranSppController::class, 'index'])->middleware('permission:pembayaran.view')->name('pembayaran-spp.index');
Route::get('pengambilan-matakuliah', [PengambilanMatakuliahController::class, 'index'])->middleware('permission:matakuliah.view')->name('pengambilan-matakuliah.index');
Route::get('peminjaman-buku', [PeminjamanBukuController::class, 'index'])->middleware('permission:buku.view')->name('peminjaman-buku.index');
Route::get('prestasi-mahasiswa', [PrestasiMahasiswaController::class, 'index'])->middleware('permission:prestasi.view')->name('prestasi-mahasiswa.index');

Route::middleware('permission:prestasi.verify')->group(function () {
Route::get('prestasi-mahasiswa/{prestasiMahasiswa}/verifikasi', [PrestasiMahasiswaController::class, 'edit'])
    ->name('prestasi-mahasiswa.verifikasi');
Route::put('prestasi-mahasiswa/{prestasiMahasiswa}/verifikasi', [PrestasiMahasiswaController::class, 'simpanVerifikasi'])
    ->name('prestasi-mahasiswa.simpan-verifikasi');
});

Route::middleware('permission:prestasi.report')->group(function () {
Route::get('laporan-prestasi', [PrestasiMahasiswaController::class, 'laporan'])->name('laporan-prestasi');
Route::get('prestasi-mahasiswa/fuzzy-kualitas', [FuzzyPrestasiController::class, 'index'])->name('prestasi-mahasiswa.fuzzy-kualitas');
Route::post('prestasi-mahasiswa/fuzzy-kualitas/hitung', [FuzzyPrestasiController::class, 'hitungUlang'])->name('prestasi-mahasiswa.fuzzy-hitung');
});

Route::middleware('permission:prestasi.create')->group(function () {
    Route::get('prestasi-mahasiswa/create', [PrestasiMahasiswaController::class, 'create'])
        ->name('prestasi-mahasiswa.create');
    Route::post('prestasi-mahasiswa', [PrestasiMahasiswaController::class, 'store'])
        ->name('prestasi-mahasiswa.store');
});

Route::middleware('permission:mahasiswa.manage')->group(function () {
    Route::get('create-mahasiswa', [MahasiswaController::class, 'create'])
        ->name('create-mahasiswa');

    Route::post('simpan-mahasiswa', [MahasiswaController::class, 'store'])
        ->name('simpan-mahasiswa');

    Route::get('edit-mahasiswa/{id}', [MahasiswaController::class, 'edit'])
        ->name('edit-mahasiswa');

    Route::put('update-mahasiswa/{id}', [MahasiswaController::class, 'update'])
        ->name('update-mahasiswa');

    Route::delete('hapus-mahasiswa/{id}', [MahasiswaController::class, 'destroy'])
        ->name('hapus-mahasiswa');
});

Route::middleware('permission:role.manage')->group(function () {
    Route::get('data-role', [RoleController::class, 'index'])->name('data-role');

    Route::get('create-role', [RoleController::class, 'create'])->name('create-role');

    Route::post('simpan-role', [RoleController::class, 'store'])->name('simpan-role');

    Route::get('edit-role/{id}', [RoleController::class, 'edit'])->name('edit-role');

    Route::put('update-role/{id}', [RoleController::class, 'update'])->name('update-role');

    Route::delete('hapus-role/{id}', [RoleController::class, 'destroy'])->name('hapus-role');
});

Route::middleware('permission:pembayaran.manage')->group(function () {
    Route::resource('pembayaran-spp', PembayaranSppController::class)->except(['index', 'show']);
});

Route::middleware('permission:matakuliah.manage')->group(function () {
    Route::resource('pengambilan-matakuliah', PengambilanMatakuliahController::class)->except(['index', 'show']);
});

Route::middleware('permission:buku.manage')->group(function () {
    Route::resource('peminjaman-buku', PeminjamanBukuController::class)->except(['index', 'show']);
});

Route::middleware('permission:prestasi.manage,prestasi.verify')->group(function () {
    Route::get('prestasi-mahasiswa/{prestasiMahasiswa}/edit', [PrestasiMahasiswaController::class, 'edit'])
        ->name('prestasi-mahasiswa.edit');
    Route::put('prestasi-mahasiswa/{prestasiMahasiswa}', [PrestasiMahasiswaController::class, 'update'])
        ->name('prestasi-mahasiswa.update');
    Route::patch('prestasi-mahasiswa/{prestasiMahasiswa}', [PrestasiMahasiswaController::class, 'update']);
});

Route::middleware('permission:prestasi.manage')->group(function () {
    Route::delete('prestasi-mahasiswa/{prestasiMahasiswa}', [PrestasiMahasiswaController::class, 'destroy'])
        ->name('prestasi-mahasiswa.destroy');
});

// ===== OPERATOR SECTION =====
Route::prefix('operator')->name('operator.')->group(function () {
    
    // Activity Logs Management
    Route::middleware('role:operator')->group(function () {
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
        Route::get('activity-logs-export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::delete('activity-logs/delete-old', [ActivityLogController::class, 'deleteOldLogs'])->name('activity-logs.delete-old');
    });

    // User Management
    Route::middleware('role:operator')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('users/{user}/activity-logs', [UserController::class, 'activityLogs'])->name('users.activity-logs');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });
});

});
