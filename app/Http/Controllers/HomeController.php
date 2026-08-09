<?php

namespace App\Http\Controllers;

use App\Models\CommitteeMember;
use App\Models\Event;
use App\Models\GalleryItem;
use App\Models\Notice;
use App\Models\Registration;
use App\Models\Sponsor;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.home', [
            'title' => 'Home',
            'stats' => $this->stats(),
            'featuredMembers' => CommitteeMember::published()
                ->where('is_featured', true)
                ->ordered()
                ->take(3)
                ->get(),
            'upcomingEvents' => Event::published()->upcoming()->take(3)->get(),
            'notices' => Notice::published()->latestFirst()->take(4)->get(),
            // The running ticker under the hero — the last five notices, at the
            // association's request.
            'tickerNotices' => Notice::published()->latestFirst()->take(5)->get(),
            'galleryItems' => GalleryItem::published()->ordered()->take(6)->get(),
            'sponsors' => Sponsor::published()->ordered()->get(),
            'categoryCounts' => Registration::verified()
                ->selectRaw('category, COUNT(*) as total')
                ->groupBy('category')
                ->pluck('total', 'category'),
            // The two most recent people to join, shown on the home page at the
            // association's request. Verified and opted-in only — the same rule
            // the full directory follows.
            'latestAlumni' => Registration::listed()->latest('id')->take(2)->get(),
            'alumniCount' => Registration::listed()->count(),
        ]);
    }

    /**
     * Counters read from live data rather than hard-coded numbers, so what the
     * home page claims can never quietly go stale.
     */
    private function stats(): array
    {
        return [
            [
                'value' => now()->year - config('rcmaa.college_founded'),
                'suffix' => '+',
                'label' => 'Years of academic legacy',
                'note' => 'Rajshahi College has taught mathematics since '.config('rcmaa.college_founded').' — among the oldest departments in the country.',
            ],
            [
                'value' => Registration::verified()->count(),
                'suffix' => '',
                'label' => 'Alumni on the register',
                'note' => 'Verified graduates who have joined the association and appear in the searchable directory.',
            ],
            [
                'value' => Registration::verified()->distinct()->count('passing_year'),
                'suffix' => '',
                'label' => 'Graduating years represented',
                'note' => 'Batches spanning generations, from the earliest records through this year\'s graduates.',
            ],
        ];
    }
}
