<?php

namespace App\Services;

use App\Models\FuzzyHasil;
use App\Models\PrestasiMahasiswa;
use Illuminate\Support\Collection;

class FuzzyPrestasiService
{
    private const TINGKAT_MAP = [
        'Kampus'      => 1,
        'Kota'        => 2,
        'Provinsi'    => 3,
        'Nasional'    => 4,
        'Internasional' => 5,
    ];

    private const KUALITAS_CENTROIDS = [
        'kurang'      => 15,
        'cukup'       => 35,
        'baik'        => 55,
        'sangat_baik' => 85,
    ];

    public function hitungSkor(
        string $tingkat,
        string $juara,
        int $jumlahPrestasi = 0,
        ?string $idPrestasi = null,
        ?string $nim = null,
    ): array {
        $tingkatVal = self::TINGKAT_MAP[$tingkat] ?? 1;
        $juaraVal = $this->parseJuara($juara);

        $tingkatMF = $this->fuzzifikasiTingkat($tingkatVal);
        $juaraMF = $this->fuzzifikasiJuara($juaraVal);
        $jmlMF = $this->fuzzifikasiJumlahPrestasi($jumlahPrestasi);

        $firedRules = $this->evaluasiAturan($tingkatMF, $juaraMF, $jmlMF);

        $aturanTerpakai = [];
        foreach ($firedRules as $kualitas => $degree) {
            $aturanTerpakai[] = [
                'output' => $kualitas,
                'degree' => round($degree, 4),
            ];
        }

        $result = $this->defuzzifikasi($firedRules);

        if ($idPrestasi !== null && $nim !== null) {
            FuzzyHasil::updateOrCreate(
                ['id_prestasi' => $idPrestasi],
                [
                    'nim' => $nim,
                    'tingkat_prestasi' => $tingkatVal,
                    'juara' => $juaraVal,
                    'jumlah_prestasi' => $jumlahPrestasi,
                    'mf_tingkat_rendah' => $tingkatMF['rendah'],
                    'mf_tingkat_sedang' => $tingkatMF['sedang'],
                    'mf_tingkat_tinggi' => $tingkatMF['tinggi'],
                    'mf_juara_1' => $juaraMF['juara_1'],
                    'mf_juara_2' => $juaraMF['juara_2'],
                    'mf_juara_3_plus' => $juaraMF['juara_3_plus'],
                    'mf_jml_sedikit' => $jmlMF['sedikit'],
                    'mf_jml_sedang' => $jmlMF['sedang'],
                    'mf_jml_banyak' => $jmlMF['banyak'],
                    'aturan_terpakai' => json_encode($aturanTerpakai),
                    'skor_fuzzy' => $result['skor'],
                    'kualitas_fuzzy' => $result['kualitas'],
                ]
            );
        }

        return $result;
    }

    public function evaluasiSemua(): Collection
    {
        $prestasi = PrestasiMahasiswa::where('status_verifikasi', 'diterima')
            ->with(['jenisPrestasi', 'tingkatPrestasi', 'mahasiswa'])
            ->get();

        foreach ($prestasi as $item) {
            if ($item->tingkatPrestasi === null || $item->mahasiswa === null) {
                continue;
            }

            $jumlahPrestasi = PrestasiMahasiswa::where('nim', $item->nim)
                ->where('status_verifikasi', 'diterima')
                ->count();

            $result = $this->hitungSkor(
                $item->tingkatPrestasi->nama_tingkat ?? 'Kampus',
                $item->juara,
                $jumlahPrestasi,
                $item->id_prestasi,
                $item->nim,
            );

            $item->update([
                'skor_fuzzy'     => $result['skor'],
                'kualitas_fuzzy' => $result['kualitas'],
            ]);
        }

        return $prestasi;
    }

    private function parseJuara(string $juara): float
    {
        if (preg_match('/(\d+)/', $juara, $matches)) {
            $angka = (float) $matches[1];
            if (stripos($juara, 'harapan') !== false) {
                $angka += 0.5;
            }
            return $angka;
        }

        return 5.0;
    }

    private function fuzzifikasiTingkat(float $value): array
    {
        return [
            'rendah' => $this->trapezoid($value, 0, 0, 2, 3),
            'sedang' => $this->triangle($value, 2, 3, 4),
            'tinggi' => $this->trapezoid($value, 3, 5, 5, 5),
        ];
    }

    private function fuzzifikasiJuara(float $value): array
    {
        return [
            'juara_1'      => $this->triangle($value, 1, 1, 2),
            'juara_2'      => $this->triangle($value, 1.5, 2, 3),
            'juara_3_plus' => $this->trapezoid($value, 2.5, 3, 5, 5),
        ];
    }

    private function fuzzifikasiJumlahPrestasi(int $value): array
    {
        return [
            'sedikit' => $this->trapezoid($value, 0, 0, 1, 3),
            'sedang'  => $this->triangle($value, 2, 4, 6),
            'banyak'  => $this->trapezoid($value, 4, 6, 999, 999),
        ];
    }

    private function evaluasiAturan(array $tingkatMF, array $juaraMF, array $jmlMF): array
    {
        $rules = [
            ['tingkat' => 'tinggi', 'juara' => 'juara_1', 'jml' => 'banyak',      'output' => 'sangat_baik'],
            ['tingkat' => 'tinggi', 'juara' => 'juara_1', 'jml' => 'sedang',      'output' => 'sangat_baik'],
            ['tingkat' => 'tinggi', 'juara' => 'juara_1', 'jml' => 'sedikit',     'output' => 'baik'],
            ['tingkat' => 'tinggi', 'juara' => 'juara_2', 'jml' => 'banyak',      'output' => 'sangat_baik'],
            ['tingkat' => 'tinggi', 'juara' => 'juara_2', 'jml' => 'sedang',      'output' => 'sangat_baik'],
            ['tingkat' => 'tinggi', 'juara' => 'juara_2', 'jml' => 'sedikit',     'output' => 'baik'],
            ['tingkat' => 'tinggi', 'juara' => 'juara_3_plus', 'jml' => 'banyak', 'output' => 'baik'],
            ['tingkat' => 'tinggi', 'juara' => 'juara_3_plus', 'jml' => 'sedang', 'output' => 'cukup'],
            ['tingkat' => 'tinggi', 'juara' => 'juara_3_plus', 'jml' => 'sedikit','output' => 'cukup'],
            ['tingkat' => 'sedang', 'juara' => 'juara_1', 'jml' => 'banyak',      'output' => 'sangat_baik'],
            ['tingkat' => 'sedang', 'juara' => 'juara_1', 'jml' => 'sedang',      'output' => 'sangat_baik'],
            ['tingkat' => 'sedang', 'juara' => 'juara_1', 'jml' => 'sedikit',     'output' => 'baik'],
            ['tingkat' => 'sedang', 'juara' => 'juara_2', 'jml' => 'banyak',      'output' => 'baik'],
            ['tingkat' => 'sedang', 'juara' => 'juara_2', 'jml' => 'sedang',      'output' => 'cukup'],
            ['tingkat' => 'sedang', 'juara' => 'juara_2', 'jml' => 'sedikit',     'output' => 'cukup'],
            ['tingkat' => 'sedang', 'juara' => 'juara_3_plus', 'jml' => 'banyak', 'output' => 'cukup'],
            ['tingkat' => 'sedang', 'juara' => 'juara_3_plus', 'jml' => 'sedang', 'output' => 'cukup'],
            ['tingkat' => 'sedang', 'juara' => 'juara_3_plus', 'jml' => 'sedikit','output' => 'kurang'],
            ['tingkat' => 'rendah', 'juara' => 'juara_1', 'jml' => 'banyak',      'output' => 'baik'],
            ['tingkat' => 'rendah', 'juara' => 'juara_1', 'jml' => 'sedang',      'output' => 'cukup'],
            ['tingkat' => 'rendah', 'juara' => 'juara_1', 'jml' => 'sedikit',     'output' => 'cukup'],
            ['tingkat' => 'rendah', 'juara' => 'juara_2', 'jml' => 'banyak',      'output' => 'cukup'],
            ['tingkat' => 'rendah', 'juara' => 'juara_2', 'jml' => 'sedang',      'output' => 'cukup'],
            ['tingkat' => 'rendah', 'juara' => 'juara_2', 'jml' => 'sedikit',     'output' => 'kurang'],
            ['tingkat' => 'rendah', 'juara' => 'juara_3_plus', 'jml' => 'banyak', 'output' => 'cukup'],
            ['tingkat' => 'rendah', 'juara' => 'juara_3_plus', 'jml' => 'sedang', 'output' => 'kurang'],
            ['tingkat' => 'rendah', 'juara' => 'juara_3_plus', 'jml' => 'sedikit','output' => 'kurang'],
        ];

        $fired = [];
        foreach ($rules as $rule) {
            $degree = min(
                $tingkatMF[$rule['tingkat']],
                $juaraMF[$rule['juara']],
                $jmlMF[$rule['jml']]
            );
            if ($degree > 0) {
                $output = $rule['output'];
                $fired[$output] = max($fired[$output] ?? 0, $degree);
            }
        }

        return $fired;
    }

    private function defuzzifikasi(array $firedRules): array
    {
        if (empty($firedRules)) {
            return ['skor' => 0, 'kualitas' => 'Kurang'];
        }

        $numerator = 0;
        $denominator = 0;

        foreach ($firedRules as $kualitas => $degree) {
            $centroid = self::KUALITAS_CENTROIDS[$kualitas];
            $numerator += $centroid * $degree;
            $denominator += $degree;
        }

        $skor = $denominator > 0 ? round($numerator / $denominator, 2) : 0;

        $kualitasLabel = match (true) {
            $skor >= 65  => 'Sangat Baik',
            $skor >= 40  => 'Baik',
            $skor >= 20  => 'Cukup',
            default      => 'Kurang',
        };

        return ['skor' => $skor, 'kualitas' => $kualitasLabel];
    }

    private function triangle(float $x, float $a, float $b, float $c): float
    {
        if ($x < $a || $x >= $c) {
            return 0.0;
        }
        if ($x <= $b) {
            return ($b !== $a) ? ($x - $a) / ($b - $a) : 1.0;
        }
        return ($c !== $b) ? ($c - $x) / ($c - $b) : 1.0;
    }

    private function trapezoid(float $x, float $a, float $b, float $c, float $d): float
    {
        if ($x <= $a || $x > $d) {
            return 0.0;
        }
        if ($x >= $b && $x <= $c) {
            return 1.0;
        }
        if ($x < $b) {
            return ($b !== $a) ? ($x - $a) / ($b - $a) : 1.0;
        }
        return ($d !== $c) ? ($d - $x) / ($d - $c) : 1.0;
    }
}
