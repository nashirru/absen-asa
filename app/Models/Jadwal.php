<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';

    protected $fillable = [
        'hari',
        'jam_mulai',
        'jam_selesai',
        'mata_pelajaran',
        'kelas_id',
        'sensei_id',
        'modul_link',
        'modul_file',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function sensei()
    {
        return $this->belongsTo(Sensei::class);
    }
}
