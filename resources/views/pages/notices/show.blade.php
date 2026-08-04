<x-layout :title="$title" :description="$description">
    <x-page-hero
        :eyebrow="$notice->published_on->format('j F Y')"
        :title="$notice->title"
        :breadcrumbs="['Notice' => route('notices.index'), $notice->title => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-narrow">
            <article class="prose-rc text-[1.02rem]" data-reveal>
                @if ($notice->excerpt)
                    <p class="!text-ink-900 text-lg font-medium">{{ $notice->excerpt }}</p>
                @endif

                @if ($notice->body)
                    @foreach (preg_split('/\n{2,}/', trim($notice->body)) as $paragraph)
                        {{-- **bold** is the only markup the CMS accepts, so it is the only
                             thing rendered as HTML here; everything else stays escaped. --}}
                        <p>{!! preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', e($paragraph)) !!}</p>
                    @endforeach
                @endif
            </article>

            @if ($notice->attachment_url)
                <a href="{{ $notice->attachment_url }}" target="_blank" rel="noopener"
                   class="card card-hover mt-10 flex items-center gap-4 p-5">
                    <span class="grid h-11 w-11 flex-none place-items-center rounded-xl bg-brass-100 text-brass-700">
                        <x-icon name="download" class="h-5 w-5"/>
                    </span>
                    <span class="flex-1">
                        <span class="block text-sm font-semibold text-ink-900">Download attachment</span>
                        <span class="block text-xs text-ink-400">Opens in a new tab</span>
                    </span>
                    <x-icon name="external" class="h-4 w-4 text-ink-300"/>
                </a>
            @endif

            @if ($recent->isNotEmpty())
                <div class="mt-16 border-t border-ink-900/10 pt-10">
                    <p class="eyebrow">More notices</p>
                    <ul class="mt-6 space-y-3">
                        @foreach ($recent as $other)
                            <li>
                                <a href="{{ route('notices.show', $other) }}"
                                   class="group flex items-baseline justify-between gap-6 border-b border-ink-900/6 py-3">
                                    <span class="text-sm text-ink-700 transition group-hover:text-brass-700">{{ $other->title }}</span>
                                    <time class="flex-none font-mono text-[0.62rem] uppercase tracking-wide text-ink-400">
                                        {{ $other->published_on->format('j M Y') }}
                                    </time>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </section>
</x-layout>
