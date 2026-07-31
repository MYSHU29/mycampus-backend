<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrestasiMahasiswa;
use App\Services\FuzzyPrestasiService;
use Illuminate\Http\JsonResponse;

class FuzzyApiController extends Controller
{
    public function index(FuzzyPrestasiService $fuzzy): JsonResponse
    {
        $prestasiMahasiswa = PrestasiMahasiswa::where('status_verifikasi', 'diterima')
            ->with(['mahasiswa', 'jenisPrestasi', 'tingkatPrestasi', 'fuzzyHasil'])
            ->latest()
            ->get();

        if ($prestasiMahasiswa->contains(fn ($p) => is_null($p->skor_fuzzy))) {
            $fuzzy->evaluasiSemua();
            $prestasiMahasiswa = PrestasiMahasiswa::where('status_verifikasi', 'diterima')
                ->with(['mahasiswa', 'jenisPrestasi', 'tingkatPrestasi', 'fuzzyHasil'])
                ->get();
        }

        $kualitasOrder = ['Sangat Baik' => 1, 'Baik' => 2, 'Cukup' => 3, 'Kurang' => 4];
        $prestasiMahasiswa = $prestasiMahasiswa
            ->sortBy(fn ($p) => $kualitasOrder[$p->kualitas_fuzzy] ?? 5)
            ->values();

        $rekapKualitas = [
            'Sangat Baik' => $prestasiMahasiswa->where('kualitas_fuzzy', 'Sangat Baik')->count(),
            'Baik' => $prestasiMahasiswa->where('kualitas_fuzzy', 'Baik')->count(),
            'Cukup' => $prestasiMahasiswa->where('kualitas_fuzzy', 'Cukup')->count(),
            'Kurang' => $prestasiMahasiswa->where('kualitas_fuzzy', 'Kurang')->count(),
        ];

        $rataRataSkor = round($prestasiMahasiswa->whereNotNull('skor_fuzzy')->avg('skor_fuzzy') ?? 0, 2);

        return response()->json([
            'success' => true,
            'data' => [
                'prestasi' => $prestasiMahasiswa,
                'rekap_kualitas' => $rekapKualitas,
                'jumlah_prestasi' => $prestasiMahasiswa->count(),
                'rata_rata_skor' => $rataRataSkor,
            ],
        ]);
    }

    public function hitungUlang(FuzzyPrestasiService $fuzzy): JsonResponse
    {
        $fuzzy->evaluasiSemua();

        return response()->json([
            'success' => true,
            'message' => 'Perhitungan fuzzy kualitas prestasi berhasil diperbarui',
        ]);
    }
}
