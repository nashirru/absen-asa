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
