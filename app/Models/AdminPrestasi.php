<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPrestasi extends Model
{
    protected $table = 'admin_prestasi';
    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
    ];

    protected $hidden = ['password'];

    public function verifikasiPrestasi()
    {
        return $this->hasMany(VerifikasiPrestasi::class, 'id_admin', 'id_admin');
    }
}
