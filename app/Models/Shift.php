<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'location_id',
        'nama_shift',
        'jam_masuk',
        'jam_keluar',
        'batas_terlambat',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
