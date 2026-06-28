<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius',
        'is_active',
        'allowed_roles',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'radius' => 'decimal:2',
            'is_active' => 'boolean',
            'allowed_roles' => 'array',
        ];
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Cek apakah role tertentu boleh akses lokasi ini.
     * Null/empty berarti semua role boleh.
     */
    public function isAllowedForRole(string $role): bool
    {
        if (empty($this->allowed_roles)) {
            return true; // null = semua role boleh
        }
        return in_array($role, $this->allowed_roles);
    }
}
