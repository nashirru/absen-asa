<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        "name",
        "type",
        "balance",
        "description",
    ];

    protected function casts(): array
    {
        return [
            "balance" => "decimal:2",
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function fromTransfers(): HasMany
    {
        return $this->hasMany(FundTransfer::class, "from_account_id");
    }

    public function toTransfers(): HasMany
    {
        return $this->hasMany(FundTransfer::class, "to_account_id");
    }
}
