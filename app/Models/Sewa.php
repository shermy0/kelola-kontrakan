<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sewa extends Model
{
    protected $table = 'sewa';
    protected $primaryKey = 'id_sewa';
    protected $fillable = ['id_penyewa', 'id_kontrakan', 'tgl_mulai', 'tgl_selesai', 'status_sewa'];

    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class, 'id_penyewa');
    }

    public function kontrakan()
    {
        return $this->belongsTo(Kontrakan::class, 'id_kontrakan');
    }
}
