<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    protected $fillable = [
        "month",
        "year",
        "status",
    ];

    protected function casts(): array
    {
        return [
            "month" => "integer",
            "year" => "integer",
        ];
    }

    public function payrollDetails(): HasMany
    {
        return $this->hasMany(PayrollDetail::class);
    }
}
