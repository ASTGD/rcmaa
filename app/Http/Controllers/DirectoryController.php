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

    /** Group key for registrants with no session — the teaching staff. */
    public const FACULTY = 'faculty';

    public function __invoke(Request $request): View
    {
        // A guest sees how many members there are and nothing else — the
        // association asked for exactly that, so the count still gives people a
        // reason to register without exposing anybody's details.
        if (! $request->attributes->get('directory_unlocked')) {
            return view('pages.directory-locked', [
                'title' => 'Alumni Directory',
                'description' => 'The RCMAA alumni directory is open to registered members.',
                'total' => Registration::listed()->count(),
                'batchCount' => Registration::listed()->whereNotNull('session')->distinct()->count('session'),
            ]);
        }

        $filters = array_filter([
            'q' => $request->string('q')->toString(),
            'session' => $request->string('session')->toString(),
            'degree' => $request->string('degree')->toString(),
            'category' => $request->string('category')->toString(),
            // Both at the association's request: find people by where they
            // live now, and by the year they passed.
            'district' => $request->string('district')->toString(),
            'passing_year' => $request->string('passing_year')->toString(),
            'profession_type' => $request->string('profession_type')->toString(),
            'work_location' => $request->string('work_location')->toString(),
        ]);

        $matching = fn () => Registration::listed()
            ->when($filters['q'] ?? null, fn ($q, $term) => $q->search($term))
            ->when($filters['degree'] ?? null, fn ($q, $degree) => $q->where('degree', $degree))
            ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('category', $category))
            ->when($filters['district'] ?? null, fn ($q, $district) => $q->where('present_district', $district))
            ->when($filters['passing_year'] ?? null, fn ($q, $year) => $q->where('passing_year', (int) $year))
            ->when($filters['profession_type'] ?? null, fn ($q, $type) => $q->where('profession_type', $type))
            ->when($filters['work_location'] ?? null, fn ($q, $location) => $q->where('work_location', $location))
            ->when($filters['session'] ?? null, fn ($q, $session) => $session === self::FACULTY
                ? $q->whereNull('session')
                : $q->where('session', $session));

        // Teachers register as staff and have no session, so they would
        // otherwise collect under a nameless empty batch. They get a group of
        // their own, shown first.
        $sessions = $matching()->whereNotNull('session')
            ->distinct()->orderByDesc('session')->pluck('session');

        $groups = $matching()->whereNull('session')->exists()
            ? $sessions->prepend(self::FACULTY)
            : $sessions;

        $page = LengthAwarePaginator::resolveCurrentPage();
        $visible = $groups->forPage($page, self::BATCHES_PER_PAGE)->values();

        $people = $matching()
            ->where(fn ($q) => $q
                ->whereIn('session', $visible->reject(fn ($g) => $g === self::FACULTY))
                ->when($visible->contains(self::FACULTY), fn ($q) => $q->orWhereNull('session')))
            ->orderBy('full_name_en')
            ->get()
            ->groupBy(fn ($r) => $r->session ?: self::FACULTY);

        // Keep the page's own order rather than whatever groupBy produced.
        $batches = $visible->mapWithKeys(fn ($key) => [$key => $people->get($key, collect())])
            ->reject(fn ($rows) => $rows->isEmpty());

        return view('pages.directory', [
            'title' => 'Alumni Directory',
            'description' => 'Verified alumni of the Department of Mathematics, Rajshahi College, listed batch by batch.',
            'batches' => $batches,
            'facultyKey' => self::FACULTY,
            'paginator' => new LengthAwarePaginator(
                $visible, $groups->count(), self::BATCHES_PER_PAGE, $page,
                ['path' => route('directory'), 'query' => $request->query()]
            ),
            'total' => $matching()->count(),
            'batchCount' => $groups->count(),
            // Every group on record, so the jump list still offers ones that the
            // current search happens to exclude.
            'allSessions' => Registration::listed()->whereNotNull('session')
                ->distinct()->orderByDesc('session')->pluck('session'),
            'hasFaculty' => Registration::listed()->whereNull('session')->exists(),
            // Only places and years someone actually lives in / passed in — a
            // filter that can only ever return nothing is noise.
            'allDistricts' => Registration::listed()->whereNotNull('present_district')
                ->distinct()->orderBy('present_district')->pluck('present_district'),
            'allPassingYears' => Registration::listed()->whereNotNull('passing_year')
                ->distinct()->orderByDesc('passing_year')->pluck('passing_year'),
            'allProfessionTypes' => config('rcmaa.options.profession_types'),
            'allWorkLocations' => Registration::listed()->whereNotNull('work_location')
                ->distinct()->orderBy('work_location')->pluck('work_location'),
            'filters' => $filters,
        ]);
    }
}
