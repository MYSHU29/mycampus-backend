<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PrestasiMahasiswa extends Model
{
    use HasUlids;

    protected $table = 'prestasi';
    protected $primaryKey = 'id_prestasi';

    protected $fillable = [
        'kode_prestasi',
        'nim',
        'id_jenis',
        'id_tingkat',
        'nama_lomba',
        'penyelenggara',
        'tanggal',
        'juara',
        'sertifikat',
        'status_verifikasi',
        'skor_fuzzy',
        'kualitas_fuzzy',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function jenisPrestasi()
    {
        return $this->belongsTo(JenisPrestasi::class, 'id_jenis', 'id_jenis');
    }

    public function tingkatPrestasi()
    {
        return $this->belongsTo(TingkatPrestasi::class, 'id_tingkat', 'id_tingkat');
    }

    public function verifikasi()
    {
        return $this->hasOne(VerifikasiPrestasi::class, 'id_prestasi', 'id_prestasi');
    }

    public function fuzzyHasil()
    {
        return $this->hasOne(FuzzyHasil::class, 'id_prestasi', 'id_prestasi');
    }
}
