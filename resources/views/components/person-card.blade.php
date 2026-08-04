@props(['person', 'showContact' => false])

{{-- Shared by committee members and faculty. Falls back to an initials seal
     when no portrait has been uploaded, which is the common case today. --}}
<article class="card card-hover group overflow-hidden" data-reveal-item>
    <div class="relative aspect-4/5 overflow-hidden bg-ink-800">
        @if ($person->photo_url)
            <img src="{{ $person->photo_url }}" alt="{{ $person->name }}" loading="lazy"
                 class="h-full w-full object-cover transition-transform duration-[900ms] ease-[cubic-bezier(.22,1,.36,1)] group-hover:scale-[1.06]">
        @else
            <div class="bg-grid-light flex h-full w-full items-center justify-center">
                <span class="heading-display text-5xl text-brass-500/80">{{ $person->initials }}</span>
            </div>
        @endif

        <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-950 via-ink-950/60 to-transparent"></div>

        <div class="absolute inset-x-0 bottom-0 p-5">
            <p class="font-mono text-[0.62rem] uppercase tracking-[0.2em] text-brass-400">
                {{ $person->designation }}
            </p>
            <h3 class="heading-display mt-1.5 text-xl text-parchment">{{ $person->name }}</h3>
            @if ($person->name_bn ?? null)
                <p lang="bn" class="mt-0.5 text-sm text-ink-300">{{ $person->name_bn }}</p>
            @endif
        </div>
    </div>

    @if ($person->batch || $person->profession || ($showContact && ($person->email || $person->phone)))
        <div class="space-y-2.5 p-5 text-sm">
            @if ($person->batch ?? null)
                <p class="flex items-center gap-2.5 text-ink-600">
                    <x-icon name="graduation" class="h-4 w-4 flex-none text-brass-600"/>
                    Session {{ $person->batch }}
                </p>
            @endif
            @if ($person->profession ?? null)
                <p class="flex items-center gap-2.5 text-ink-600">
                    <x-icon name="briefcase" class="h-4 w-4 flex-none text-brass-600"/>
                    {{ $person->profession }}
                </p>
            @endif
            @if ($showContact && $person->email)
                <a href="mailto:{{ $person->email }}" class="flex items-center gap-2.5 text-ink-600 transition hover:text-brass-700">
                    <x-icon name="mail" class="h-4 w-4 flex-none text-brass-600"/>
                    <span class="truncate">{{ $person->email }}</span>
                </a>
            @endif
            @if ($showContact && $person->phone)
                <a href="tel:{{ preg_replace('/\D/', '', $person->phone) }}"
                   class="flex items-center gap-2.5 text-ink-600 transition hover:text-brass-700">
                    <x-icon name="phone" class="h-4 w-4 flex-none text-brass-600"/>
                    {{ $person->phone }}
                </a>
            @endif
        </div>
    @endif
</article>
