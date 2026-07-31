<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuzzyHasil extends Model
{
    protected $table = 'fuzzy_hasil';
    protected $primaryKey = 'id_fuzzy_hasil';

    protected $fillable = [
        'id_prestasi',
        'nim',
        'tingkat_prestasi',
        'juara',
        'jumlah_prestasi',
        'mf_tingkat_rendah',
        'mf_tingkat_sedang',
        'mf_tingkat_tinggi',
        'mf_juara_1',
        'mf_juara_2',
        'mf_juara_3_plus',
        'mf_jml_sedikit',
        'mf_jml_sedang',
        'mf_jml_banyak',
        'aturan_terpakai',
        'skor_fuzzy',
        'kualitas_fuzzy',
    ];

    protected $casts = [
        'mf_tingkat_rendah' => 'float',
        'mf_tingkat_sedang' => 'float',
        'mf_tingkat_tinggi' => 'float',
        'mf_juara_1' => 'float',
        'mf_juara_2' => 'float',
        'mf_juara_3_plus' => 'float',
        'mf_jml_sedikit' => 'float',
        'mf_jml_sedang' => 'float',
        'mf_jml_banyak' => 'float',
        'skor_fuzzy' => 'float',
    ];

    public function prestasi()
    {
        return $this->belongsTo(PrestasiMahasiswa::class, 'id_prestasi', 'id_prestasi');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }
}
