<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use Publishable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_head' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->diskUrl($this->photo_path);
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', preg_replace('/^(Prof\.|Dr\.|Md\.|Mst\.)\s*/i', '', $this->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
