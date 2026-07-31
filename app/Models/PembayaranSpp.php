<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranSpp extends Model
{
    protected $table = 'pembayaran_spp';

    protected $fillable = [
        'nim',
        'kode_bayar',
        'semester',
        'tahun_akademik',
        'jumlah_bayar',
        'metode_bayar',
        'status_bayar',
        'tanggal_bayar',
        'bukti_bayar',
        'keterangan',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }
}
