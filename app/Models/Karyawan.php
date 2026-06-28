<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';

    protected $fillable = [
        'user_id',
        'nik',
        'jabatan',
        'divisi',
        'alamat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
