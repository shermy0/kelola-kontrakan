<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penyewa extends Model
{
    protected $table = 'penyewa';
    protected $primaryKey = 'id_penyewa';
    protected $fillable = ['nama_lengkap', 'no_telepon', 'nik', 'alamat'];

    public function sewa()
    {
        return $this->hasMany(Sewa::class, 'id_penyewa');
    }
}