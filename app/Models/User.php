<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'foto',
        'status_aktif',
        'device_uuid',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status_aktif' => 'boolean',
        ];
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class);
    }

    public function sensei()
    {
        return $this->hasOne(Sensei::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }

    public function isSensei(): bool
    {
        return $this->role === 'sensei';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'siswa' => 'Siswa',
            'karyawan' => 'Karyawan',
            'sensei' => 'Sensei',
            default => ucfirst($this->role),
        };
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('uploads/foto/' . $this->foto);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=2563EB&color=fff&size=200';
    }

}
