<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kontrakan extends Model
{
    protected $table = 'kontrakan';
    protected $primaryKey = 'id_kontrakan';
    protected $fillable = ['nomor_unit', 'harga_sewa', 'status', 'keterangan'];

    public function sewa()
    {
        return $this->hasMany(Sewa::class, 'id_kontrakan');
    }
}
