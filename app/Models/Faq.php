<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use Publishable;

    public const CATEGORIES = [
        'general' => 'General',
        'membership' => 'Membership',
        'registration' => 'Reunion Registration',
        'payment' => 'Payment',
        'events' => 'Events',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
