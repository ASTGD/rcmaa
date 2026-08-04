<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DirectoryController extends Controller
{
    /**
     * The alumni directory, grouped batch by batch.
     *
     * The association asked for it this way: somebody looking for their own
     * cohort should find it as a block rather than page through everyone in
     * passing-year order. Pagination therefore counts batches, not people, so a
     * batch is never split across two pages.
     *
     * Built from verified registrations only. Name, session, profession and
     * mobile are published — at the association's instruction, and stated on
     * both the registration form and the privacy policy. Anyone can withdraw
     * from their own portal.
     */
    private const BATCHES_PER_PAGE = 6;

    public function __invoke(Request $request): View
    {
        $filters = array_filter([
            'q' => $request->string('q')->toString(),
            'session' => $request->string('session')->toString(),
            'degree' => $request->string('degree')->toString(),
        ]);

        $matching = fn () => Registration::listed()
            ->when($filters['q'] ?? null, fn ($q, $term) => $q->search($term))
            ->when($filters['session'] ?? null, fn ($q, $session) => $q->where('session', $session))
            ->when($filters['degree'] ?? null, fn ($q, $degree) => $q->where('degree', $degree));

        // Newest batch first — most registrants are recent graduates looking
        // for their own year.
        $sessions = $matching()->distinct()->orderByDesc('session')->pluck('session');

        $page = LengthAwarePaginator::resolveCurrentPage();
        $visible = $sessions->forPage($page, self::BATCHES_PER_PAGE)->values();

        $batches = $matching()
            ->whereIn('session', $visible)
            ->orderBy('full_name_en')
            ->get()
            ->groupBy('session')
            ->sortKeysDesc();

        return view('pages.directory', [
            'title' => 'Alumni Directory',
            'description' => 'Verified alumni of the Department of Mathematics, Rajshahi College, listed batch by batch.',
            'batches' => $batches,
            'paginator' => new LengthAwarePaginator(
                $visible, $sessions->count(), self::BATCHES_PER_PAGE, $page,
                ['path' => route('directory'), 'query' => $request->query()]
            ),
            'total' => $matching()->count(),
            'batchCount' => $sessions->count(),
            // Every batch on record, so the jump list still offers batches that
            // the current search happens to exclude.
            'allSessions' => Registration::listed()->distinct()->orderByDesc('session')->pluck('session'),
            'filters' => $filters,
        ]);
    }
}
