<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Directory"
        title="Alumni directory"
        lead="Verified graduates of the Department of Mathematics, Rajshahi College — listed batch by batch. Find your own session, or search by name."
        :breadcrumbs="['Directory' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc">

            {{-- Filters --}}
            <form method="GET" action="{{ route('directory') }}" class="card p-5 md:p-6" data-reveal>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[1fr_11rem_12rem_12rem_auto] xl:items-end">
                    <div>
                        <label for="q" class="field-label">Search by name</label>
                        <input id="q" name="q" type="search" class="input" placeholder="Name…"
                               value="{{ $filters['q'] ?? '' }}">
                    </div>
                    <div>
                        <label for="session" class="field-label">
                            Batch <span lang="bn" class="field-label-bn">&middot; ব্যাচ</span>
                        </label>
                        <select id="session" name="session" class="input">
                            <option value="">All batches</option>
                            @if ($hasFaculty)
                                <option value="{{ $facultyKey }}" @selected(($filters['session'] ?? null) === $facultyKey)>
                                    Teachers &amp; Staff
                                </option>
                            @endif
                            @foreach ($allSessions as $session)
                                <option value="{{ $session }}" @selected(($filters['session'] ?? null) === $session)>
                                    {{ $session }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Category is one of the three the association asked members to
                         be able to filter by: name, session and category. --}}
                    <div>
                        <label for="category" class="field-label">
                            Category <span lang="bn" class="field-label-bn">&middot; ক্যাটাগরি</span>
                        </label>
                        <select id="category" name="category" class="input">
                            <option value="">All categories</option>
                            @foreach (config('rcmaa.registration.categories') as $key => $c)
                                <option value="{{ $key }}" @selected(($filters['category'] ?? null) === $key)>{{ $c['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="degree" class="field-label">Degree</label>
                        <select id="degree" name="degree" class="input">
                            <option value="">All degrees</option>
                            @foreach (config('rcmaa.options.degrees') as $key => $label)
                                <option value="{{ $key }}" @selected(($filters['degree'] ?? null) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-ink h-[3.05rem] flex-1">
                            <x-icon name="search" class="h-4 w-4"/>Search
                        </button>
                        @if ($filters)
                            <a href="{{ route('directory') }}" class="btn btn-outline h-[3.05rem]" aria-label="Clear filters">
                                <x-icon name="x" class="h-4 w-4"/>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- Jump straight to a batch --}}
            @if ($allSessions->count() + ($hasFaculty ? 1 : 0) > 1)
                <nav class="mt-8" aria-label="Jump to a batch" data-reveal>
                    <p class="font-mono text-[0.6rem] uppercase tracking-[0.16em] text-ink-400">Jump to batch</p>
                    <ul class="mt-3 flex flex-wrap gap-1.5">
                        @if ($hasFaculty)
                            <li>
                                <a href="{{ route('directory', ['session' => $facultyKey]) }}"
                                   @class([
                                       'inline-block rounded-lg border px-2.5 py-1 font-mono text-[0.72rem] transition',
                                       'border-brass-500 bg-brass-500 text-ink-950' => ($filters['session'] ?? null) === $facultyKey,
                                       'border-ink-900/12 text-ink-600 hover:border-brass-500 hover:text-brass-700' => ($filters['session'] ?? null) !== $facultyKey,
                                   ])>Teachers</a>
                            </li>
                        @endif
                        @foreach ($allSessions as $session)
                            <li>
                                <a href="{{ route('directory', ['session' => $session]) }}"
                                   @class([
                                       'inline-block rounded-lg border px-2.5 py-1 font-mono text-[0.72rem] transition',
                                       'border-brass-500 bg-brass-500 text-ink-950' => ($filters['session'] ?? null) === $session,
                                       'border-ink-900/12 text-ink-600 hover:border-brass-500 hover:text-brass-700' => ($filters['session'] ?? null) !== $session,
                                   ])>{{ $session }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif

            <p class="mt-8 text-sm text-ink-500" data-reveal>
                {{ number_format($total) }} {{ Str::plural('member', $total) }} across {{ number_format($batchCount) }} {{ Str::plural('group', $batchCount) }}@if ($filters) matching your search @endif
            </p>

            @if ($batches->isNotEmpty())
                <div class="mt-10 space-y-14">
                    @foreach ($batches as $session => $people)
                        <section id="batch-{{ Str::slug($session) }}" class="scroll-mt-28" data-reveal>
                            {{-- Batch heading --}}
                            <div class="flex flex-wrap items-baseline gap-x-4 gap-y-1 border-b border-ink-900/10 pb-3">
                                @if ($session === $facultyKey)
                                    <h2 class="heading-display text-2xl text-ink-950">Teachers &amp; Staff</h2>
                                    <p lang="bn" class="font-bangla text-sm text-ink-500">শিক্ষকমণ্ডলী</p>
                                @else
                                    <h2 class="heading-display text-2xl text-ink-950">Session {{ $session }}</h2>
                                    <p lang="bn" class="font-bangla text-sm text-ink-500">সেশন {{ $session }}</p>
                                @endif
                                <span class="ml-auto font-mono text-[0.68rem] uppercase tracking-[0.14em] text-brass-700">
                                    {{ $people->count() }} {{ Str::plural($session === $facultyKey ? 'member' : 'graduate', $people->count()) }}
                                </span>
                            </div>

                            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                                 data-reveal data-reveal-stagger="0.05">
                                @foreach ($people as $person)
                                    <article class="card card-hover flex gap-4 p-5" data-reveal-item>
                                        <span class="grid h-12 w-12 flex-none place-items-center overflow-hidden rounded-xl bg-ink-900 text-brass-500">
                                            @if ($person->photo_url)
                                                <img src="{{ $person->photo_url }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                            @else
                                                <span class="heading-display text-base">
                                                    {{ mb_strtoupper(mb_substr(preg_replace('/^(Md\.|Mst\.|Mrs\.|Mr\.|Dr\.)\s*/i', '', $person->full_name_en), 0, 1)) }}
                                                </span>
                                            @endif
                                        </span>

                                        <div class="min-w-0">
                                            <h3 class="truncate text-[0.95rem] font-semibold text-ink-950">{{ $person->full_name_en }}</h3>
                                            @if ($person->full_name_bn)
                                                <p lang="bn" class="truncate text-xs text-ink-400">{{ $person->full_name_bn }}</p>
                                            @endif

                                            <p class="mt-2 flex items-center gap-1.5 text-[0.75rem] text-ink-500">
                                                <x-icon name="graduation" class="h-3.5 w-3.5 flex-none text-brass-600"/>
                                                @if ($person->category === 'teacher')
                                                    Department of Mathematics
                                                @else
                                                    {{ $person->passing_year ? 'Passed '.$person->passing_year : 'Currently studying' }}
                                                @endif
                                            </p>

                                            @if ($person->profession)
                                                <p class="mt-1 flex items-center gap-1.5 text-[0.75rem] text-ink-500">
                                                    <x-icon name="briefcase" class="h-3.5 w-3.5 flex-none text-brass-600"/>
                                                    <span class="truncate">{{ $person->profession }}</span>
                                                </p>
                                            @endif

                                            {{-- Published at the association's instruction; the Privacy Policy
                                                 and the registration form both say so plainly. --}}
                                            @if ($person->mobile)
                                                <a href="tel:{{ preg_replace('/\D/', '', $person->mobile) }}"
                                                   class="mt-1 flex items-center gap-1.5 text-[0.75rem] text-ink-500 transition hover:text-brass-700">
                                                    <x-icon name="phone" class="h-3.5 w-3.5 flex-none text-brass-600"/>
                                                    <span class="truncate">{{ $person->mobile }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>

                <div class="mt-14">{{ $paginator->links() }}</div>
            @else
                <x-empty-state class="mt-6" icon="search"
                    title="No alumni match this search"
                    message="The directory is built from verified reunion registrations. If you have registered and cannot find yourself, your payment may still be awaiting verification.">
                    <div class="mt-6 flex flex-wrap justify-center gap-3">
                        <a href="{{ route('register.create') }}" class="btn btn-primary btn-sm">Join the Directory</a>
                        <a href="{{ route('registration.status') }}" class="btn btn-outline btn-sm">Check Your Status</a>
                    </div>
                </x-empty-state>
            @endif
        </div>
    </section>
</x-layout>
