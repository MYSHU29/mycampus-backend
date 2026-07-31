<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPrestasi extends Model
{
    protected $table = 'jenis_prestasi';
    protected $primaryKey = 'id_jenis';

    protected $fillable = ['nama_jenis'];

    public function prestasiMahasiswa()
    {
        return $this->hasMany(PrestasiMahasiswa::class, 'id_jenis', 'id_jenis');
    }
}
