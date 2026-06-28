<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollDetail extends Model
{
    protected $fillable = [
        "payroll_period_id",
        "employee_id",
        "karyawan_id",
        "base_salary",
        "total_allowance",
        "total_deduction",
        "bonus",
        "net_salary",
        "paid_at",
    ];

    protected function casts(): array
    {
        return [
            "base_salary" => "decimal:2",
            "total_allowance" => "decimal:2",
            "total_deduction" => "decimal:2",
            "bonus" => "decimal:2",
            "net_salary" => "decimal:2",
            "paid_at" => "datetime",
        ];
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
}
