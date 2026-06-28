<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensei extends Model
{
    protected $table = 'sensei';

    protected $fillable = [
        'user_id',
        'mata_pelajaran',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}
