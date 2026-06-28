<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    protected $table = 'karyawan';

    protected $fillable = [
        'user_id',
        'nik',
        'jabatan',
        'divisi',
        'alamat',
        'base_salary',
        'join_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'base_salary' => 'decimal:2',
            'join_date' => 'date',
            'status' => 'string',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salaryComponents(): HasMany
    {
        return $this->hasMany(SalaryComponent::class, 'karyawan_id');
    }

    public function payrollDetails(): HasMany
    {
        return $this->hasMany(PayrollDetail::class, 'karyawan_id');
    }
}
