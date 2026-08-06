<section class="bg-parchment-dim py-24 md:py-32">
    <div class="container-rc">
        <x-section-heading
            align="center"
            eyebrow="Directory & Community"
            title="Grow and develop your network everywhere"
            lead="RCMAA is more than a register of names. It is a working network of teachers, researchers, civil servants, engineers and entrepreneurs who share one classroom in common."/>

        {{-- The two most recent people to join, as the association asked. --}}
        @if ($latestAlumni->isNotEmpty())
            <div class="mx-auto mt-16 max-w-4xl" data-reveal>
                <div class="flex items-baseline justify-between gap-4">
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-brass-700">
                        Recently joined
                    </p>
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.14em] text-ink-400">
                        {{ number_format($alumniCount) }} listed
                    </p>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2" data-reveal data-reveal-stagger="0.1">
                    @foreach ($latestAlumni as $person)
                        <article class="card flex items-center gap-4 p-5" data-reveal-item>
                            <span class="grid h-14 w-14 flex-none place-items-center overflow-hidden rounded-2xl bg-ink-900 text-brass-500">
                                @if ($person->photo_url)
                                    <img src="{{ $person->photo_url }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                @else
                                    <span class="heading-display text-lg">
                                        {{ mb_strtoupper(mb_substr(preg_replace('/^(Md\.|Mst\.|Mrs\.|Mr\.|Dr\.)\s*/i', '', $person->full_name_en), 0, 1)) }}
                                    </span>
                                @endif
                            </span>

                            <div class="min-w-0">
                                <h3 class="truncate font-semibold text-ink-950">{{ $person->full_name_en }}</h3>
                                @if ($person->full_name_bn)
                                    <p lang="bn" class="truncate text-xs text-ink-400">{{ $person->full_name_bn }}</p>
                                @endif

                                {{-- Teachers register as staff and have no session, which read
                                     as a bare "Session ·" with nothing after it. --}}
                                <p class="mt-1.5 flex items-center gap-1.5 text-[0.76rem] text-ink-500">
                                    <x-icon name="graduation" class="h-3.5 w-3.5 flex-none text-brass-600"/>
                                    <span class="truncate">
                                        {{ $person->session ? 'Session '.$person->session : 'Department of Mathematics' }}
                                        @if ($person->profession) &middot; {{ $person->profession }} @endif
                                    </span>
                                </p>
                            </div>

                            <a href="{{ $person->session
                                          ? route('directory', ['session' => $person->session])
                                          : route('directory') }}"
                               class="ml-auto flex-none text-ink-400 transition hover:text-brass-700"
                               aria-label="{{ $person->session ? 'See the '.$person->session.' batch' : 'Open the directory' }}">
                                <x-icon name="arrow-up-right" class="h-4 w-4"/>
                            </a>
                        </article>
                    @endforeach
                </div>

                {{-- mt-10, not mt-6. The cards above reveal by sliding up from
                     y+34px, and a 24px gap is less than that — so on the way in
                     they travelled down across this button and covered it. The
                     clearance has to exceed the animation's own offset. --}}
                <div class="mt-10 text-center">
                    <a href="{{ route('directory') }}" class="btn btn-outline btn-sm">
                        View Directory
                        <x-icon name="arrow-right" class="h-3.5 w-3.5"/>
                    </a>
                </div>
            </div>
        @endif

        <div class="mt-16 grid gap-6 md:grid-cols-3" data-reveal data-reveal-stagger="0.1">
            @foreach ([
                ['icon' => 'users', 'title' => 'Student Organizations & Clubs', 'body' => 'The departmental club, olympiad teams and study circles that shaped generations of students — still active, still recruiting, and still supported by alumni mentors.', 'link' => ['Browse the directory', 'directory']],
                ['icon' => 'globe', 'title' => 'Arts, Culture & Sports', 'body' => 'From the annual cultural evening to inter-departmental tournaments, alumni return each year to compete, perform and cheer alongside current students.', 'link' => ['See the gallery', 'gallery']],
                ['icon' => 'briefcase', 'title' => 'Careers & Mentorship', 'body' => 'Graduates in academia, banking, the civil service and industry offer guidance, referrals and research collaboration to those still finding their footing.', 'link' => ['How to join', 'how-to-apply']],
            ] as $card)
                <article class="card card-hover group flex flex-col p-8" data-reveal-item>
                    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-ink-900 text-brass-500 transition-colors duration-500 group-hover:bg-brass-500 group-hover:text-ink-950">
                        <x-icon :name="$card['icon']" class="h-6 w-6"/>
                    </span>

                    <h3 class="heading-display mt-7 text-xl text-ink-950">{{ $card['title'] }}</h3>
                    <p class="prose-rc mt-3 flex-1 text-sm">{{ $card['body'] }}</p>

                    <a href="{{ route($card['link'][1]) }}"
                       class="mt-6 inline-flex items-center gap-2 text-[0.78rem] font-semibold uppercase tracking-[0.1em] text-brass-700 transition-colors hover:text-ink-950">
                        {{ $card['link'][0] }}
                        <x-icon name="arrow-up-right" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"/>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
