<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'user_id',
        'location_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'latitude',
        'longitude',
        'accuracy',
        'distance',
        'radius',
        'status',
        'device',
        'browser',
        'ip_address',
        'selfie_check_in',
        'selfie_check_out',
        'catatan',
        'shift',
        'shift_id',
        'is_approved',
        'is_lembur',
        'jam_lembur_mulai',
        'jam_lembur_selesai',
        'durasi_lembur',
        'latitude_keluar',
        'longitude_keluar',
        'accuracy_keluar',
        'distance_keluar',
        'is_mocked',
        'is_anomaly',
        'anomaly_details',
        'tanggal_keluar',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'tanggal_keluar' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'accuracy' => 'decimal:2',
            'distance' => 'decimal:2',
            'radius' => 'decimal:2',
            'is_approved' => 'boolean',
            'is_lembur' => 'boolean',
            'is_mocked' => 'boolean',
            'is_anomaly' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'hadir' => 'bg-green-100 text-green-800',
            'terlambat' => 'bg-yellow-100 text-yellow-800',
            'izin' => 'bg-blue-100 text-blue-800',
            'sakit' => 'bg-orange-100 text-orange-800',
            'alpha' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'hadir' => 'hadir',
            'terlambat' => 'terlambat',
            'izin' => 'izin',
            'sakit' => 'sakit',
            'alpha' => 'alpha',
            default => 'unknown',
        };
    }

    public function getSelfieCheckInUrlAttribute(): ?string
    {
        if ($this->selfie_check_in && file_exists(public_path('uploads/selfie/' . $this->selfie_check_in))) {
            return asset('uploads/selfie/' . $this->selfie_check_in);
        }
        return null;
    }

    public function getSelfieCheckOutUrlAttribute(): ?string
    {
        if ($this->selfie_check_out && file_exists(public_path('uploads/selfie/' . $this->selfie_check_out))) {
            return asset('uploads/selfie/' . $this->selfie_check_out);
        }
        return null;
    }

    public function getDurasiLemburFormattedAttribute(): ?string
    {
        if (!$this->durasi_lembur) return null;
        $jam = floor($this->durasi_lembur);
        $menit = round(($this->durasi_lembur - $jam) * 60);
        return $jam . ' jam ' . $menit . ' menit';
    }
}
