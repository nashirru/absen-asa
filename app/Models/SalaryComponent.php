<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryComponent extends Model
{
    protected $fillable = [
        "karyawan_id",
        "name",
        "type",
        "amount",
    ];

    protected function casts(): array
    {
        return [
            "amount" => "decimal:2",
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
}
