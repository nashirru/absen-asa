<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        "name",
        "position",
        "department",
        "base_salary",
        "join_date",
        "status",
    ];

    protected function casts(): array
    {
        return [
            "base_salary" => "decimal:2",
            "join_date" => "date",
        ];
    }

    public function salaryComponents(): HasMany
    {
        return $this->hasMany(SalaryComponent::class);
    }

    public function payrollDetails(): HasMany
    {
        return $this->hasMany(PayrollDetail::class);
    }
}
