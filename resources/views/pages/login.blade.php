<x-layout :title="$title" :description="$description">
    <section data-theme="dark" class="relative flex min-h-[calc(100svh-7.6rem)] items-center overflow-hidden bg-ink-950 py-20">
        <div class="bg-grid-light pointer-events-none absolute inset-0"></div>
        <div class="pointer-events-none absolute -top-24 -left-24 h-[26rem] w-[26rem] rounded-full bg-ink-500/25 blur-[120px]"></div>
        <div class="pointer-events-none absolute -bottom-32 -right-24 h-[24rem] w-[24rem] rounded-full bg-brass-700/18 blur-[120px]"></div>

        <div class="container-rc relative grid gap-14 lg:grid-cols-2 lg:items-center lg:gap-20">

            {{-- Pitch --}}
            <div class="hidden lg:block">
                <p class="eyebrow eyebrow-light" data-reveal>Committee</p>
                <h1 class="heading-display mt-6 max-w-lg text-[clamp(2rem,3.6vw,3rem)] text-parchment" data-reveal="split">
                    Committee sign in
                </h1>
                <p class="prose-rc mt-6 max-w-md !text-ink-300" data-reveal data-reveal-delay="0.15">
                    Committee accounts manage registrations, verify payments and publish notices, events
                    and gallery photographs.
                </p>

                <div class="mt-10 rounded-2xl border border-white/10 bg-ink-900/60 p-6" data-reveal data-reveal-delay="0.25">
                    <p class="text-sm font-semibold text-parchment">Registered for the reunion?</p>
                    <p class="mt-1.5 text-sm text-ink-400">
                        You don't need an account. We'll email you a secure link to manage your
                        registration, print your entry pass and control your directory listing.
                    </p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <a href="{{ route('portal.request') }}" class="btn btn-primary btn-sm">Manage my registration</a>
                        <a href="{{ route('registration.status') }}" class="btn btn-outline-light btn-sm">Check status</a>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="mx-auto w-full max-w-md" data-reveal="scale">
                <div class="rounded-3xl border border-white/10 bg-ink-900/80 p-8 backdrop-blur-sm md:p-10">
                    <x-logo light :wordmark="false" class="mb-7"/>

                    <h2 class="heading-display text-2xl text-parchment">Welcome back</h2>
                    <p class="mt-1.5 text-sm text-ink-400">Sign in with your committee credentials.</p>

                    @if ($errors->any())
                        <div class="mt-6 rounded-xl border border-red-500/25 bg-red-500/10 px-4 py-3 text-sm text-red-200" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="field-label !text-ink-200">Email address</label>
                            <input id="email" name="email" type="email" required autofocus autocomplete="username"
                                   value="{{ old('email') }}"
                                   class="input !border-white/12 !bg-ink-800/60 !text-parchment placeholder:!text-ink-500"
                                   placeholder="you@rcmaa.bd">
                        </div>

                        <div>
                            <label for="password" class="field-label !text-ink-200">Password</label>
                            <input id="password" name="password" type="password" required autocomplete="current-password"
                                   class="input !border-white/12 !bg-ink-800/60 !text-parchment placeholder:!text-ink-500"
                                   placeholder="••••••••">
                        </div>

                        <label class="flex items-center gap-2.5 text-sm text-ink-300">
                            <input type="checkbox" name="remember" value="1"
                                   class="h-4 w-4 rounded border-white/20 bg-ink-800 text-brass-500 focus:ring-brass-600">
                            Keep me signed in
                        </label>

                        <button type="submit" class="btn btn-primary w-full">
                            Sign In
                            <x-icon name="arrow-right" class="h-4 w-4"/>
                        </button>
                    </form>

                    <p class="mt-7 border-t border-white/8 pt-6 text-center text-xs text-ink-500">
                        Lost your password? Contact the association administrator on
                        {{ config('rcmaa.contact.hotline') }}.
                    </p>
                </div>

                <p class="mt-6 text-center text-sm text-ink-400 lg:hidden">
                    Registered for the reunion?
                    <a href="{{ route('portal.request') }}" class="text-brass-400 underline underline-offset-4">
                        Manage your registration
                    </a>
                </p>
            </div>
        </div>
    </section>
</x-layout>
