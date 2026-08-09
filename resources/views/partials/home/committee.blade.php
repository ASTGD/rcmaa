<section data-theme="dark" class="relative overflow-hidden bg-ink-900 py-24 md:py-32">
    <div class="bg-grid-light pointer-events-none absolute inset-0"></div>
    <div class="pointer-events-none absolute top-1/4 -left-40 h-[30rem] w-[30rem] rounded-full bg-brass-700/12 blur-[130px]"></div>

    <div class="container-rc relative">
        <div class="grid gap-10 lg:grid-cols-[1fr_auto] lg:items-end">
            <x-section-heading
                light
                eyebrow="Committee"
                title="Fostering mathematical excellence and future leaders"
                lead="The Department of Mathematics at Rajshahi College is committed to nurturing analytical thinking, research, and academic leadership. We empower our students with strong foundational knowledge and problem-solving skills to excel in higher education and diverse professional careers worldwide."/>

            <a href="{{ route('committee') }}" class="btn btn-outline-light flex-none" data-reveal data-reveal-delay="0.2">
                All Committees
                <x-icon name="arrow-right" class="h-4 w-4"/>
            </a>
        </div>

        @if ($featuredMembers->isNotEmpty())
            <div class="mt-16 grid justify-center gap-6 sm:grid-cols-2 lg:grid-cols-3"
                 data-reveal data-reveal-stagger="0.1">
                @foreach ($featuredMembers as $member)
                    <article class="group relative overflow-hidden rounded-2xl border border-white/8 bg-ink-800/60 transition-all duration-500 hover:border-brass-600/50 hover:bg-ink-800 mx-auto w-full max-w-xs"
                             data-reveal-item>
                        <div class="relative aspect-4/5 overflow-hidden bg-ink-800">
                            @if ($member->photo_url)
                                <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" loading="eager"
                                     class="h-full w-full object-cover object-top transition-transform duration-[900ms] ease-[cubic-bezier(.22,1,.36,1)] group-hover:scale-[1.06]">
                            @else
                                <div class="bg-grid-light grid h-full place-items-center">
                                    <span class="heading-display text-5xl text-brass-500/70">{{ $member->initials }}</span>
                                </div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-950 to-transparent"></div>

                            <div class="absolute inset-x-0 bottom-0 p-6">
                                <p class="font-mono text-[0.62rem] uppercase tracking-[0.2em] text-brass-400">
                                    {{ $member->designation }}
                                </p>
                                <h3 class="heading-display mt-2 text-xl text-parchment">{{ $member->name }}</h3>
                                @if ($member->name_bn)
                                    <p lang="bn" class="mt-1 text-xs text-ink-300">{{ $member->name_bn }}</p>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <x-empty-state class="mt-16 !border-white/15 !bg-ink-800/40" icon="users"
                title="Committee members not published yet"
                message="Add members from the admin area and mark them as featured to show them here."/>
        @endif
    </div>
</section>
