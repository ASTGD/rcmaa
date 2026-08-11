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
                {{-- Six filters now, so they wrap as a grid of equal cells with
                     the buttons on their own row rather than one long rail. --}}
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
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

                    {{-- By place — where members live now, from the structured
                         address the form collects. --}}
                    <div>
                        <label for="district" class="field-label">
                            Place <span lang="bn" class="field-label-bn">&middot; জেলা</span>
                        </label>
                        <select id="district" name="district" class="input">
                            <option value="">All districts</option>
                            @foreach ($allDistricts as $district)
                                <option value="{{ $district }}" @selected(($filters['district'] ?? null) === $district)>{{ $district }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="passing_year" class="field-label">
                            Passing Year <span lang="bn" class="field-label-bn">&middot; পাসের বছর</span>
                        </label>
                        <select id="passing_year" name="passing_year" class="input">
                            <option value="">All years</option>
                            @foreach ($allPassingYears as $year)
                                <option value="{{ $year }}" @selected(($filters['passing_year'] ?? null) == $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="profession_type" class="field-label">
                            Profession Type <span lang="bn" class="field-label-bn">&middot; পেশার ধরণ</span>
                        </label>
                        <select id="profession_type" name="profession_type" class="input">
                            <option value="">All profession types</option>
                            @foreach ($allProfessionTypes as $key => $label)
                                <option value="{{ $key }}" @selected(($filters['profession_type'] ?? null) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="work_location" class="field-label">
                            Work Location <span lang="bn" class="field-label-bn">&middot; কর্মস্থলের জেলা</span>
                        </label>
                        <select id="work_location" name="work_location" class="input">
                            <option value="">All locations</option>
                            @foreach ($allWorkLocations as $location)
                                <option value="{{ $location }}" @selected(($filters['work_location'] ?? null) === $location)>{{ $location }}</option>
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

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <h3 class="truncate text-[0.95rem] font-semibold text-ink-950">{{ $person->full_name_en }}</h3>
                                                @if ($person->linkedin_url)
                                                    <a href="{{ $person->linkedin_url }}" target="_blank" rel="noopener" class="text-brass-600 hover:text-ink-950 transition flex-none" title="LinkedIn Profile">
                                                        <x-icon name="linkedin" class="h-4 w-4"/>
                                                    </a>
                                                @endif
                                            </div>
                                            @if ($person->full_name_bn)
                                                <p lang="bn" class="truncate text-xs text-ink-400">{{ $person->full_name_bn }}</p>
                                            @endif
                                            @if ($person->session)
                                                <p class="text-xs text-ink-500 mt-0.5">Session: {{ $person->session }}</p>
                                            @endif

                                            @if ($person->passing_year)
                                                <p class="mt-2 flex items-center gap-1.5 text-[0.75rem] text-ink-500">
                                                    <x-icon name="calendar" class="h-3.5 w-3.5 flex-none text-brass-600"/>
                                                    <span>Passing Year: {{ $person->passing_year }}</span>
                                                </p>
                                            @endif

                                            @if ($person->category === 'teacher')
                                                <p class="mt-1 flex items-center gap-1.5 text-[0.75rem] font-semibold text-brass-700">
                                                    <x-icon name="graduation" class="h-3.5 w-3.5 flex-none text-brass-600"/>
                                                    <span>{{ $person->teacher_type === 'staff' ? 'Employee / Officer (কর্মকর্তা/কর্মচারী)' : 'Teacher (শিক্ষক)' }}</span>
                                                </p>
                                            @endif

                                            @if ($person->profession_type === 'student' || $person->category === 'current_student')
                                                <p class="mt-1 flex items-center gap-1.5 text-[0.75rem] font-semibold text-brass-700">
                                                    <x-icon name="book" class="h-3.5 w-3.5 flex-none text-brass-600"/>
                                                    <span>Student (শিক্ষার্থী)</span>
                                                </p>
                                            @else
                                                @if ($person->designation)
                                                    <p class="mt-1 flex items-center gap-1.5 text-[0.75rem] text-ink-500">
                                                        <x-icon name="user" class="h-3.5 w-3.5 flex-none text-brass-600"/>
                                                        <span class="truncate">{{ $person->designation }}</span>
                                                    </p>
                                                @endif
                                                @if ($person->organization)
                                                    <p class="mt-1 flex items-center gap-1.5 text-[0.75rem] text-ink-500">
                                                        <x-icon name="briefcase" class="h-3.5 w-3.5 flex-none text-brass-600"/>
                                                        <span class="truncate">{{ $person->organization }}</span>
                                                    </p>
                                                @endif
                                                @if ($person->work_location)
                                                    <p class="mt-1 flex items-center gap-1.5 text-[0.75rem] text-ink-500">
                                                        <x-icon name="map-pin" class="h-3.5 w-3.5 flex-none text-brass-600"/>
                                                        <span class="truncate">Work Location: {{ $person->work_location }}</span>
                                                    </p>
                                                @endif
                                            @endif

                                            {{-- Published at the association's instruction; the Privacy Policy
                                                 and the registration form both say so plainly. --}}
                                            @if ($person->mobile)
                                                <a href="tel:{{ preg_replace('/\D/', '', $person->mobile) }}"
                                                   class="mt-2 flex items-center gap-1.5 text-[0.75rem] text-ink-500 transition hover:text-brass-700">
                                                    <x-icon name="phone" class="h-3.5 w-3.5 flex-none text-brass-600"/>
                                                    <span class="truncate">{{ $person->mobile }}</span>
                                                </a>
                                            @endif
                                            @if ($person->email)
                                                <a href="mailto:{{ $person->email }}"
                                                   class="mt-1 flex items-center gap-1.5 text-[0.75rem] text-ink-500 transition hover:text-brass-700">
                                                    <x-icon name="mail" class="h-3.5 w-3.5 flex-none text-brass-600"/>
                                                    <span class="truncate">{{ $person->email }}</span>
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
                        @auth('alumni')
                            <a href="{{ route('member.dashboard') }}" class="btn btn-primary btn-sm">My Account</a>
                        @else
                            <a href="{{ route('register.create') }}" class="btn btn-primary btn-sm">Join the Directory</a>
                        @endauth
                        <a href="{{ route('registration.status') }}" class="btn btn-outline btn-sm">Check Your Status</a>
                    </div>
                </x-empty-state>
            @endif
        </div>
    </section>
</x-layout>
