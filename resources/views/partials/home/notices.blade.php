{{--
    Dedicated notice section — the association pointed out the home page had an
    Events section but nowhere for notices. Latest four, pinned first, linking
    through to the full list the site has always had at /notice.
--}}
@if ($notices->isNotEmpty())
    <section class="bg-parchment-dim py-20 md:py-28">
        <div class="container-rc">
            <div class="grid gap-10 lg:grid-cols-[1fr_auto] lg:items-end">
                <x-section-heading
                    eyebrow="নোটিশ · Notice Board"
                    title="Announcements from the association"
                    lead="Official notices about the reunion, registration and the association's work — the newest first."/>

                <a href="{{ route('notices.index') }}" class="btn btn-outline flex-none" data-reveal data-reveal-delay="0.2">
                    All Notices
                    <x-icon name="arrow-right" class="h-4 w-4"/>
                </a>
            </div>

            <div class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-reveal data-reveal-stagger="0.08">
                @foreach ($notices as $notice)
                    <a href="{{ route('notices.show', $notice) }}"
                       class="card card-hover group flex flex-col p-6" data-reveal-item>
                        <div class="flex items-center justify-between gap-3">
                            <time datetime="{{ $notice->published_on?->toDateString() }}"
                                  class="font-mono text-[0.65rem] uppercase tracking-[0.14em] text-ink-400">
                                {{ $notice->published_on?->format('j M Y') }}
                            </time>
                            @if ($notice->is_pinned)
                                <span class="rounded-full bg-brass-100 px-2 py-0.5 font-mono text-[0.58rem] font-semibold uppercase tracking-[0.12em] text-brass-800">
                                    Pinned
                                </span>
                            @endif
                        </div>

                        <h3 class="mt-3 flex-1 text-[0.95rem] leading-snug font-semibold text-ink-950">
                            {{ $notice->title }}
                        </h3>

                        @if ($notice->excerpt)
                            <p class="mt-2 line-clamp-3 text-[0.8rem] leading-relaxed text-ink-500">{{ $notice->excerpt }}</p>
                        @endif

                        <span class="mt-4 inline-flex items-center gap-1.5 text-[0.72rem] font-semibold uppercase tracking-[0.1em] text-brass-700">
                            Read notice
                            <x-icon name="arrow-up-right" class="h-3 w-3 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"/>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
