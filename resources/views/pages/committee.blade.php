<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Committee"
        :title="$groups[$active]['en']"
        lead="Fostering mathematical excellence and future leaders. The committees below run the association's day-to-day work and the Grand Reunion 2026."
        :breadcrumbs="['Committee' => null]">
        <p lang="bn" class="mt-4 text-lg text-brass-400" data-reveal data-reveal-delay="0.25">
            {{ $groups[$active]['bn'] }}
        </p>
    </x-page-hero>

    <section class="bg-parchment py-14 md:py-20">
        <div class="container-rc">
            {{-- Committee switcher --}}
            <nav aria-label="Committees" class="flex flex-wrap gap-2" data-reveal>
                @foreach ($groups as $key => $labels)
                    <a href="{{ route('committee', ['group' => $key]) }}"
                       @class([
                           'rounded-full px-4 py-2.5 text-[0.8rem] font-medium transition-all duration-300',
                           'bg-ink-900 text-parchment' => $key === $active,
                           'bg-white text-ink-600 ring-1 ring-ink-900/8 hover:bg-brass-100 hover:text-ink-900' => $key !== $active,
                       ])
                       @if ($key === $active) aria-current="page" @endif>
                        {{ $labels['en'] }}
                    </a>
                @endforeach
            </nav>

            @if ($members->isNotEmpty())
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                     data-reveal data-reveal-stagger="0.08">
                    @foreach ($members as $member)
                        <x-person-card :person="$member"/>
                    @endforeach
                </div>
            @else
                <x-empty-state class="mt-12" icon="users"
                    :title="'No members listed for the '.$groups[$active]['en'].' yet'"
                    message="Members added from Admin → Committee will appear here."/>
            @endif
        </div>
    </section>

    @include('partials.leadership-messages')

    @include('partials.cta')
</x-layout>
