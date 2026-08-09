{{--
Member sign-in. The association's specification asks for four things on this
page: email-and-password credentials, a visible "forgot password" link, a
"need help" route to the committee, and a way back to registration for
anyone who has not registered yet. All four are here.
--}}
<x-layout :title="$title" :description="$description">
    <x-page-hero eyebrow="Members" title="Sign in to your account"
        lead="Manage your reunion registration, download your slips, and search the alumni directory."
        :breadcrumbs="['Member login' => null]" />

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc grid max-w-5xl gap-8 lg:grid-cols-[1fr_19rem] lg:items-start">

            <div class="card p-6 md:p-8">
                @if (session('status'))
                    <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
                @endif
                @if (session('directory_gate'))
                    <x-alert type="info" class="mb-6">{{ session('directory_gate') }}</x-alert>
                @endif

                <h2 class="heading-display text-xl text-ink-950">Member login</h2>
                <p lang="bn" class="mt-1 font-bangla text-sm text-ink-500">সদস্য লগইন</p>

                <form method="POST" action="{{ route('member.login.attempt') }}" class="mt-7">
                    @csrf

                    <div class="grid gap-6">
                        <x-field name="email" type="email" autocomplete="email" required :model="false"
                            label="Email address" bn="ইমেইল" :value="old('email')"
                            hint="The address you registered with." />

                        <div x-data="{ show: false }">
                            <div class="flex items-baseline justify-between gap-3">
                                <label for="password" class="field-label">
                                    Password <span lang="bn" class="field-label-bn">&middot; পাসওয়ার্ড</span>
                                    <span class="text-brass-700">*</span>
                                </label>
                                <a href="{{ route('member.password.request') }}"
                                    class="text-[0.78rem] font-medium text-brass-700 underline underline-offset-4 transition hover:text-ink-950">
                                    Forgot password?
                                </a>
                            </div>
                            <div class="relative mt-2">
                                <input id="password" name="password" :type="show ? 'text' : 'password'" required
                                    autocomplete="current-password" class="input w-full pr-10">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-1 flex items-center pr-3 text-ink-500 hover:text-ink-950">
                                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.058m4.09-4.09A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21m-4.2-4.2L3 3" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                            <p class="field-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <label class="choice mt-6 !items-center !border-0 !bg-transparent !px-0 !py-0">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        <span class="choice-box" aria-hidden="true"></span>
                        <span class="text-[0.85rem] font-normal">Keep me signed in on this device</span>
                    </label>

                    <button type="submit" class="btn btn-primary mt-7 w-full">Sign in</button>
                </form>

                <div class="mt-8 border-t border-ink-900/8 pt-6">
                    <p class="text-sm text-ink-600">
                        Don't have an account?
                        <a href="{{ route('register.create') }}"
                            class="font-semibold text-brass-700 underline underline-offset-4 transition hover:text-ink-950">
                            Register here
                        </a>
                    </p>
                    <p lang="bn" class="mt-1 font-bangla text-xs text-ink-400">
                        অ্যাকাউন্ট নেই? রেজিস্ট্রেশন করুন।
                    </p>
                </div>

                {{-- Registrations made before member accounts existed have no
                password, and their owners cannot be told to remember one. --}}
                <details class="mt-6 border-t border-ink-900/8 pt-6">
                    <summary class="cursor-pointer text-sm font-medium text-ink-700 transition hover:text-brass-700">
                        Registered before member accounts existed?
                    </summary>
                    <p class="prose-rc mt-3 text-sm">
                        If you registered early you may not have set a password. Enter your address
                        and we will email you a one-time link that signs you in and lets you choose one.
                    </p>
                    <form method="POST" action="{{ route('member.link.send') }}" class="mt-4">
                        @csrf
                        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off"
                            aria-hidden="true">
                        <label for="link-email" class="sr-only">Email address</label>
                        <div class="flex flex-wrap gap-2">
                            <input id="link-email" name="email" type="email" required class="input min-w-0 flex-1"
                                placeholder="you@example.com">
                            <button type="submit" class="btn btn-outline">Send me a link</button>
                        </div>
                    </form>
                </details>
            </div>

            {{-- Need help --}}
            <aside class="space-y-4 lg:sticky lg:top-28">
                <div class="card p-5">
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-brass-700">Need help?</p>
                    <p lang="bn" class="mt-2 font-bangla text-sm text-ink-600">
                        লগইন করতে সমস্যা হলে যোগাযোগ করুন।
                    </p>
                    <p class="mt-1 text-xs text-ink-400">
                        If you cannot sign in, the registration helpline can look you up by name and session.
                    </p>

                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-xs text-ink-400">Registration helpline</dt>
                            <dd>
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', config('rcmaa.contact.helpline')) }}"
                                    class="font-medium text-ink-900 transition hover:text-brass-700">
                                    {{ config('rcmaa.contact.helpline') }}
                                </a>
                                <span
                                    class="block text-xs text-ink-400">{{ config('rcmaa.contact.helpline_hours') }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-ink-400">Email</dt>
                            <dd>
                                <a href="mailto:{{ config('rcmaa.contact.email') }}"
                                    class="font-medium break-all text-ink-900 transition hover:text-brass-700">
                                    {{ config('rcmaa.contact.email') }}
                                </a>
                            </dd>
                        </div>
                    </dl>

                    <a href="{{ config('rcmaa.contact.whatsapp') }}" target="_blank" rel="noopener"
                        class="btn btn-outline btn-sm mt-5 w-full">WhatsApp the committee</a>
                    <a href="{{ route('faqs') }}" class="btn btn-ghost btn-sm mt-2 w-full">Read the FAQ</a>
                </div>

                <div class="card p-5">
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-brass-700">Committee</p>
                    <p class="mt-2 text-xs text-ink-500">
                        Committee members administering the site sign in separately.
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm mt-3 w-full">Committee sign-in</a>
                </div>
            </aside>
        </div>
    </section>
</x-layout>