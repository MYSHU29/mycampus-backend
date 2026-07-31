<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiPrestasi extends Model
{
    protected $table = 'verifikasi_prestasi';
    protected $primaryKey = 'id_verifikasi';

    protected $fillable = [
        'id_prestasi',
        'id_admin',
        'tanggal_verifikasi',
        'catatan',
    ];

    public function prestasi()
    {
        return $this->belongsTo(PrestasiMahasiswa::class, 'id_prestasi', 'id_prestasi');
    }

    public function admin()
    {
        return $this->belongsTo(AdminPrestasi::class, 'id_admin', 'id_admin');
    }
}
