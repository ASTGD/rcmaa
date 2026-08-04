<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Membership"
        title="What membership gives you"
        lead="RCMAA membership is not a certificate on a wall. These are the things it actually gets you."
        :breadcrumbs="['Features' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-reveal data-reveal-stagger="0.08">
            @foreach ([
                ['search', 'A searchable directory', 'Find classmates by name, session or passing year. Every listing is a verified graduate, not a guess.'],
                ['calendar', 'Priority on events', 'Reunions, seminars and general meetings, with registration opening to members first.'],
                ['users', 'Batch networks', 'Reconnect with your own session through batch representatives who keep their groups active.'],
                ['briefcase', 'Career support', 'Mentorship, referrals and guidance from alumni already established in academia, government and industry.'],
                ['bell', 'Official announcements', 'Notices from the committee delivered on the site rather than lost in a group chat.'],
                ['graduation', 'Give back to the department', 'A structured way to support current students — prizes, resources, and help for those in need.'],
                ['shield', 'Verified membership', 'Payments are checked by hand and every entry is confirmed before it reaches the directory.'],
                ['camera', 'The shared record', 'A growing archive of photographs and milestones from across the department\'s history.'],
                ['globe', 'A global community', 'Graduates spread far beyond Rajshahi, held together by one register and one point of contact.'],
            ] as [$icon, $heading, $body])
                <article class="card card-hover group p-7" data-reveal-item>
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-ink-900 text-brass-500 transition-colors duration-500 group-hover:bg-brass-500 group-hover:text-ink-950">
                        <x-icon :name="$icon" class="h-5 w-5"/>
                    </span>
                    <h2 class="heading-display mt-6 text-lg text-ink-950">{{ $heading }}</h2>
                    <p class="prose-rc mt-2.5 text-sm">{{ $body }}</p>
                </article>
            @endforeach
        </div>
    </section>

    @include('partials.cta')
</x-layout>
