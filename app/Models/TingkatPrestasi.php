<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TingkatPrestasi extends Model
{
    protected $table = 'tingkat_prestasi';
    protected $primaryKey = 'id_tingkat';

    protected $fillable = ['nama_tingkat'];

    public function prestasiMahasiswa()
    {
        return $this->hasMany(PrestasiMahasiswa::class, 'id_tingkat', 'id_tingkat');
    }
}
