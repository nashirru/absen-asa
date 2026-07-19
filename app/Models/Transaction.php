<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        "type",
        "account_id",
        "category_id",
        "jenis_pengeluaran",
        "amount",
        "description",
        "date",
        "attachment",
        "ref_payroll_id",
    ];

    protected function casts(): array
    {
        return [
            "amount" => "decimal:2",
            "date" => "date",
            "jenis_pengeluaran" => "array",
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
