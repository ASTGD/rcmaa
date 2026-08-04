<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use Publishable;

    public const TIERS = [
        'title' => 'Title Sponsor',
        'platinum' => 'Platinum',
        'gold' => 'Gold',
        'partner' => 'Partner',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->diskUrl($this->logo_path);
    }

    public function getTierLabelAttribute(): string
    {
        return self::TIERS[$this->tier] ?? $this->tier;
    }
}
