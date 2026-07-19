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
        'is_24_hours',
    ];

    protected function casts(): array
    {
        return [
            'is_24_hours' => 'boolean',
        ];
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
