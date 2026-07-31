<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $primaryKey = 'nim';      // PK menggunakan nim bukan id
    public $incrementing  = false;      // PK bukan auto increment
    protected $keyType    = 'string';   // PK bertipe string

    protected $fillable = [
        'nim',
        'nama',
        'email',
        'no_telp',
        'tanggal_lahir',
        'jenis_kelamin',
        'kota_asal',
        'alamat',
        'prodi',
        'fakultas',
        'angkatan',
        'semester',
        'ipk',
        'status',
        'foto',
        'catatan',
    ];
}