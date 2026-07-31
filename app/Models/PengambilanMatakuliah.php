<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengambilanMatakuliah extends Model
{
    protected $table = 'pengambilan_matakuliah';

    protected $fillable = [
        'nim',
        'kode_matkul',
        'nama_matkul',
        'sks',
        'dosen',
        'semester',
        'tahun_akademik',
        'status',
        'nilai_akhir',
        'grade',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }
}
