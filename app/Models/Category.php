<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        "name",
        "type",
        "color",
        "sub_categories",
    ];

    protected function casts(): array
    {
        return [
            "sub_categories" => "array",
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
