<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    protected $fillable = ['tanggal', 'keterangan'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    /**
     * Get all holiday dates for a given month/year as array of 'Y-m-d' strings.
     */
    public static function getDatesForMonth(int $year, int $month): array
    {
        return static::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->pluck('tanggal')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();
    }

    /**
     * Get holiday map keyed by 'Y-m-d' => keterangan for a given month/year.
     */
    public static function getMapForMonth(int $year, int $month): array
    {
        return static::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get()
            ->mapWithKeys(fn($h) => [Carbon::parse($h->tanggal)->format('Y-m-d') => $h->keterangan])
            ->toArray();
    }
}
