<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Your registration"
        title="Manage your registration"
        lead="No password needed. Enter the email address you registered with and we'll send you a secure link."
        :breadcrumbs="['My registration' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-narrow">
            @if (session('status'))
                <x-alert type="success" title="Check your inbox" class="mb-8">{{ session('status') }}</x-alert>
            @endif

            <form method="POST" action="{{ route('portal.send-link') }}" class="card relative p-6 md:p-8">
                @csrf
                <div class="absolute -left-[9999px]" aria-hidden="true">
                    <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div class="grid gap-5 sm:grid-cols-[1fr_auto] sm:items-end">
                    <div>
                        <label for="email" class="field-label">
                            Email address
                            <span lang="bn" class="field-label-bn"> &middot; নিবন্ধনের সময় দেওয়া ইমেইল</span>
                        </label>
                        <input id="email" name="email" type="email" class="input" required
                               placeholder="you@example.com" value="{{ old('email') }}"
                               @error('email') aria-invalid="true" @enderror>
                        @error('email')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn btn-ink h-[3.05rem]">
                        <x-icon name="mail" class="h-4 w-4"/>Send me a link
                    </button>
                </div>

                <p class="field-hint mt-4">
                    The link lasts one hour. Use it to correct your details, print your entry pass,
                    or remove yourself from the public directory.
                </p>
            </form>

            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                @foreach ([
                    ['book', 'Correct your details', 'Mobile, address, T-shirt size and workplace — no phone call needed.'],
                    ['download', 'Print your pass', 'Take it to the registration desk on the day.'],
                    ['lock', 'Directory control', 'Choose whether you appear in the public alumni directory.'],
                ] as [$icon, $heading, $body])
                    <div class="card p-5" data-reveal>
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-brass-100 text-brass-700">
                            <x-icon :name="$icon" class="h-4.5 w-4.5"/>
                        </span>
                        <h2 class="heading-display mt-4 text-base text-ink-950">{{ $heading }}</h2>
                        <p class="prose-rc mt-1.5 text-[0.82rem]">{{ $body }}</p>
                    </div>
                @endforeach
            </div>

            <p class="mt-8 text-center text-sm text-ink-500">
                Only want to check whether your payment cleared?
                <a href="{{ route('registration.status') }}" class="font-medium text-brass-700 underline underline-offset-4">
                    Look up your status
                </a> with your reference number instead.
            </p>
        </div>
    </section>
</x-layout>
