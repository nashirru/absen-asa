<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryComponent extends Model
{
    protected $fillable = [
        "employee_id",
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
