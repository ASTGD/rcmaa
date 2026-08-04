<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CommitteeMember extends Model
{
    use Publishable;

    /**
     * The four committees named in the association's navigation specification,
     * keyed so the nav stays a single dropdown.
     */
    public const COMMITTEES = [
        'advisory' => ['en' => 'Advisory Committee', 'bn' => 'উপদেষ্টা কমিটি'],
        'reunion_convening' => ['en' => 'Convening Committee', 'bn' => 'আহ্বায়ক কমিটি'],
        'reunion_sub' => ['en' => 'Reunion Sub-Committee', 'bn' => 'রিইউনিয়ন উপকমিটি'],
        'batch_rep' => ['en' => 'Batch Representatives', 'bn' => 'ব্যাচ প্রতিনিধি'],
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function scopeOfCommittee(Builder $query, string $committee): Builder
    {
        return $query->where('committee', $committee);
    }

    public function getCommitteeLabelAttribute(): string
    {
        return self::COMMITTEES[$this->committee]['en'] ?? $this->committee;
    }

    public function getCommitteeLabelBnAttribute(): ?string
    {
        return self::COMMITTEES[$this->committee]['bn'] ?? null;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->diskUrl($this->photo_path);
    }

    /** "MRI" — used for the fallback avatar when no photo is uploaded. */
    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', preg_replace('/^(Md\.|Mst\.|Mrs\.|Mr\.|Dr\.)\s*/i', '', $this->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
