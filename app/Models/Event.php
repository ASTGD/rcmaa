<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use Publishable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'registration_open' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('starts_on', '>=', today())->orderBy('starts_on');
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->whereDate('starts_on', '<', today())->orderByDesc('starts_on');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->diskUrl($this->cover_path);
    }

    public function getIsPastAttribute(): bool
    {
        return $this->starts_on->isBefore(today());
    }

    /** Days until the event; negative once it has passed. */
    public function getCountdownDaysAttribute(): int
    {
        return (int) today()->diffInDays($this->starts_on, false);
    }
}
