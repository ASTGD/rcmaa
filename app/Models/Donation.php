<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'amount' => 'decimal:2',
        ];
    }
}
