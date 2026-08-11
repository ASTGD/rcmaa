{{--
    What a visitor who is not signed in sees at /directory.

    The association's specification: "Public/guest visitors will only see the
    total count of registered members." So the count is here, and nothing else —
    no names, no sessions, no photographs.
--}}
<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Directory"
        title="Alumni directory"
        lead="A verified register of graduates of the Department of Mathematics, Rajshahi College — open to registered members."
        :breadcrumbs="['Directory' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc max-w-3xl">

            <div class="card overflow-hidden text-center">
                <div class="bg-ink-900 px-6 py-12">
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.2em] text-brass-400">
                        Members registered
                    </p>
                    <p class="heading-display mt-4 text-7xl leading-none text-parchment" data-count="{{ $total }}">0</p>
                    @if ($batchCount)
                        <p class="mt-4 text-sm text-ink-300">
                            across {{ number_format($batchCount) }} {{ Str::plural('session', $batchCount) }}
                        </p>
                    @endif
                </div>

                <div class="p-7 md:p-9">
                    <span class="inline-grid h-12 w-12 place-items-center rounded-2xl bg-brass-100 text-brass-700">
                        <x-icon name="users" class="h-5 w-5"/>
                    </span>

                    <h2 class="heading-display mt-5 text-2xl text-ink-950">Members only</h2>
                    <p lang="bn" class="mt-3 font-bangla text-[0.95rem] leading-[1.9] text-ink-600">
                        শুধুমাত্র RCMAA-এর নিবন্ধিত সদস্যরাই লগইন করার মাধ্যমে অ্যালামনাই ডিরেক্টরি দেখতে পারবেন।
                    </p>
                    <p class="prose-rc mt-3">
                        The directory lists each member's name, session, profession, photograph and
                        mobile number, so it is shown only to registered members who have signed in.
                        Signed-in members can search it by name, session or category.
                    </p>

                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        <a href="{{ route('member.login') }}" class="btn btn-primary">Sign in to view</a>
                        @unless (auth('alumni')->check())
                            <a href="{{ route('register.create') }}" class="btn btn-outline">Register for the Reunion</a>
                        @endunless
                    </div>

                    <p class="mt-6 text-xs text-ink-400">
                        Registered but never set a password?
                        <a href="{{ route('member.password.request') }}" class="text-brass-700 underline underline-offset-4">
                            Get a link by email
                        </a>.
                    </p>
                </div>
            </div>
        </div>
    </section>
</x-layout>
