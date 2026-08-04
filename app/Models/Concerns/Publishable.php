<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

/** Shared behaviour for every CMS-managed content model. */
trait Publishable
{
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Resolves a stored path on the public disk, or null when unset.
     *
     * Stamped with the file's own modification time. Replacing a photo usually
     * means keeping its filename — same person, better crop — and without this
     * every visitor who had already seen the old one keeps it until their cache
     * expires. The two featured portraits were re-cropped on the server and the
     * site still showed the originals; this is the reason.
     */
    protected function diskUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $disk = Storage::disk('public');
        $url = $disk->url($path);

        // A missing file is the template's problem to render around, not ours.
        try {
            $stamp = $disk->lastModified($path);
        } catch (\Throwable) {
            return $url;
        }

        return $url.(str_contains($url, '?') ? '&' : '?').'v='.$stamp;
    }
}
