<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use Publishable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'published_on' => 'date',
            'is_pinned' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Pinned notices float regardless of date. */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('is_pinned')->orderByDesc('published_on');
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->diskUrl($this->attachment_path);
    }
}
