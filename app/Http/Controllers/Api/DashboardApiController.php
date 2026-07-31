<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisPrestasi;
use App\Models\Mahasiswa;
use App\Models\PrestasiMahasiswa;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    public function index(): JsonResponse
    {
        $statusVerifikasi = [
            'menunggu' => PrestasiMahasiswa::where('status_verifikasi', 'menunggu')->count(),
            'diterima' => PrestasiMahasiswa::where('status_verifikasi', 'diterima')->count(),
            'ditolak' => PrestasiMahasiswa::where('status_verifikasi', 'ditolak')->count(),
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

        return response()->json([
            'success' => true,
            'data' => [
                'jumlah_mahasiswa' => Mahasiswa::count(),
                'jumlah_referensi' => JenisPrestasi::count(),
                'jumlah_pendaftaran' => PrestasiMahasiswa::count(),
                'jumlah_capaian' => PrestasiMahasiswa::where('status_verifikasi', 'diterima')->count(),
                'status_verifikasi' => $statusVerifikasi,
                'prestasi_per_jenis' => $prestasiPerJenis,
                'prestasi_per_tingkat' => $prestasiPerTingkat,
                'tren_bulanan' => $trenBulanan,
            ],
        ]);
    }
}
