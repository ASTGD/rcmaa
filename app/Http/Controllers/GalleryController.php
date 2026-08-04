<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\Contracts\View\View;

class GalleryController extends Controller
{
    public function __invoke(): View
    {
        $items = GalleryItem::published()->ordered()->get();

        return view('pages.gallery', [
            'title' => 'Gallery',
            'description' => 'A glimpse into the moments, milestones and collective efforts that bring our alumni community together.',
            'items' => $items,
            // Only offer filters that actually have photos behind them.
            'categories' => collect(GalleryItem::CATEGORIES)
                ->filter(fn ($label, $key) => $items->contains('category', $key))
                ->all(),
        ]);
    }
}
