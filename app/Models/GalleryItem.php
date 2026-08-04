<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    use Publishable;

    public const CATEGORIES = [
        'campus' => 'Campus Life',
        'department' => 'Department',
        'events' => 'Events & Meetings',
        'awards' => 'Awards',
        'clubs' => 'Clubs & Competitions',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->diskUrl($this->image_path);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
