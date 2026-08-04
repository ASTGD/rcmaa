<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Notice Board"
        title="Announcements from the association"
        lead="Official notices from the Rajshahi College Mathematics Alumni Association — registration deadlines, meetings and committee decisions."
        :breadcrumbs="['Notice' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-narrow">
            @if ($notices->isNotEmpty())
                <ul class="space-y-4" data-reveal data-reveal-stagger="0.08">
                    @foreach ($notices as $notice)
                        <li data-reveal-item>
                            <a href="{{ route('notices.show', $notice) }}"
                               class="card card-hover group flex gap-5 p-6">
                                <span @class([
                                    'grid h-12 w-12 flex-none place-items-center rounded-xl transition-colors duration-500',
                                    'bg-brass-500 text-ink-950' => $notice->is_pinned,
                                    'bg-ink-900/5 text-brass-700 group-hover:bg-brass-100' => ! $notice->is_pinned,
                                ])>
                                    <x-icon :name="$notice->is_pinned ? 'bell' : 'book'" class="h-5 w-5"/>
                                </span>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <time class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-brass-700"
                                              datetime="{{ $notice->published_on->toDateString() }}">
                                            {{ $notice->published_on->format('j F Y') }}
                                        </time>
                                        @if ($notice->is_pinned)
                                            <span class="rounded-full bg-brass-200 px-2.5 py-0.5 text-[0.62rem] font-semibold uppercase tracking-wide text-brass-900">
                                                Pinned
                                            </span>
                                        @endif
                                    </div>

                                    <h2 class="heading-display mt-2 text-xl text-ink-950 transition-colors group-hover:text-brass-700">
                                        {{ $notice->title }}
                                    </h2>

                                    @if ($notice->excerpt)
                                        <p class="prose-rc mt-2 line-clamp-2 text-sm">{{ $notice->excerpt }}</p>
                                    @endif
                                </div>

                                <x-icon name="arrow-up-right"
                                        class="mt-1 h-4 w-4 flex-none text-ink-300 transition-all duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-brass-600"/>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-10">{{ $notices->links() }}</div>
            @else
                <x-empty-state icon="bell" title="No notices published"
                    message="Announcements posted from Admin → Notices will appear here."/>
            @endif
        </div>
    </section>
</x-layout>
