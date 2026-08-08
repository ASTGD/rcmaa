{{--
    Running notice ticker — the last five notices, scrolling under the hero, at
    the association's request. initMarquees drives it from the data-marquee
    attributes and pauses on hover; with no JS the strip still shows the first
    items, just standing still.

    If no notice has been published yet, the strip carries the association's
    own opening line rather than sitting empty.
--}}
@php
    $tickerItems = $tickerNotices->isNotEmpty()
        ? $tickerNotices->map(fn ($n) => ['label' => $n->title, 'url' => route('notices.show', $n)])
        : collect([[
            'label' => 'খুব শীঘ্রই রেজিষ্ট্রেশন কার্যক্রম শুরু হতে যাচ্ছে। আমাদের গ্রান্ড রিইউনিয়ন: Math Nexus - RCMAA Reunion 2026 আগামী ১৯ শে ডিসেম্বর ২০২৬।',
            'url' => route('notices.index'),
        ]]);
@endphp

<div class="relative flex items-stretch overflow-hidden border-b border-white/10 bg-ink-900"
     role="region" aria-label="Latest notices">
    {{-- The fixed label; the strip scrolls behind it. --}}
    <a href="{{ route('notices.index') }}"
       class="relative z-10 flex flex-none items-center gap-2 bg-brass-500 px-4 py-2.5 text-ink-950 transition hover:bg-brass-400 sm:px-5">
        <x-icon name="bell" class="h-3.5 w-3.5"/>
        <span class="font-mono text-[0.62rem] font-semibold uppercase tracking-[0.16em]">Notice</span>
        <span lang="bn" class="hidden font-bangla text-[0.72rem] font-semibold sm:inline">নোটিশ</span>
    </a>

    <div class="min-w-0 flex-1" data-marquee="45">
        <div class="flex w-max items-center" data-marquee-track>
            @foreach ($tickerItems as $item)
                <a href="{{ $item['url'] }}"
                   class="flex items-center gap-3 py-2.5 pl-8 text-[0.8rem] text-ink-200 transition-colors hover:text-brass-400">
                    <span class="h-1.5 w-1.5 flex-none rounded-full bg-brass-500/70"></span>
                    <span lang="bn" class="whitespace-nowrap font-bangla">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
