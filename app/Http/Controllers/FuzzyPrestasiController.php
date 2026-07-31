<?php

namespace App\Http\Controllers;

use App\Models\PrestasiMahasiswa;
use App\Services\FuzzyPrestasiService;

class FuzzyPrestasiController extends Controller
{
    public function index(FuzzyPrestasiService $fuzzy)
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
            'Baik'        => $prestasiMahasiswa->where('kualitas_fuzzy', 'Baik')->count(),
            'Cukup'       => $prestasiMahasiswa->where('kualitas_fuzzy', 'Cukup')->count(),
            'Kurang'      => $prestasiMahasiswa->where('kualitas_fuzzy', 'Kurang')->count(),
        ];

        $jumlahPrestasi = $prestasiMahasiswa->count();

        $tingkatBreakdown = $prestasiMahasiswa
            ->groupBy(fn ($p) => $p->tingkatPrestasi->nama_tingkat ?? 'Tidak Diketahui')
            ->map(fn ($items) => $items->count())
            ->sortKeys()
            ->toArray();

        $juaraList = $prestasiMahasiswa->pluck('juara')->filter();
        $peringkatTerbaik = '-';
        $frekuensiTerbaik = 0;
        if ($juaraList->isNotEmpty()) {
            $parsed = $juaraList->map(function ($j) {
                preg_match('/(\d+)/', $j, $m);
                return $m ? (int) $m[1] : 999;
            });
            $terendah = $parsed->min();
            $peringkatTerbaik = 'Juara ' . $terendah;
            $frekuensiTerbaik = $juaraList->filter(fn ($j) => str_contains($j, (string) $terendah))->count();
        }

        $rataRataSkor = round($prestasiMahasiswa->whereNotNull('skor_fuzzy')->avg('skor_fuzzy') ?? 0, 2);
        $layakCount = $prestasiMahasiswa->where('skor_fuzzy', '>=', 70)->count();
        $tidakLayakCount = $prestasiMahasiswa->where('skor_fuzzy', '<', 50)->count();

        $rataRataPerTingkat = $prestasiMahasiswa
            ->filter(fn ($p) => $p->skor_fuzzy !== null)
            ->groupBy(fn ($p) => $p->tingkatPrestasi->nama_tingkat ?? 'Tidak Diketahui')
            ->map(fn ($items) => round($items->avg('skor_fuzzy'), 2))
            ->sortKeys()
            ->toArray();

        $rataRataPerJenis = $prestasiMahasiswa
            ->filter(fn ($p) => $p->skor_fuzzy !== null)
            ->groupBy(fn ($p) => $p->jenisPrestasi->nama_jenis ?? 'Tidak Diketahui')
            ->map(fn ($items) => round($items->avg('skor_fuzzy'), 2))
            ->sortKeys()
            ->toArray();

        return view('prestasi-mahasiswa.fuzzy-kualitas', compact(
            'prestasiMahasiswa', 'rekapKualitas',
            'jumlahPrestasi', 'tingkatBreakdown', 'peringkatTerbaik', 'frekuensiTerbaik',
            'rataRataSkor', 'layakCount', 'tidakLayakCount',
            'rataRataPerTingkat', 'rataRataPerJenis'
        ));
    }

    public function hitungUlang(FuzzyPrestasiService $fuzzy)
    {
        $fuzzy->evaluasiSemua();

        return redirect()->route('prestasi-mahasiswa.fuzzy-kualitas')
            ->with('success', 'Perhitungan fuzzy kualitas prestasi berhasil diperbarui.');
    }
}
