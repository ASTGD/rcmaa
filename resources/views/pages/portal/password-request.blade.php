<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Members"
        title="Forgot your password"
        lead="Enter the email address you registered with and we will send you a link to choose a new one."
        :breadcrumbs="['Member login' => route('member.login'), 'Forgot password' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc max-w-xl">
            <div class="card p-6 md:p-8">
                @if (session('status'))
                    <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
                @endif

                <form method="POST" action="{{ route('member.password.email') }}">
                    @csrf
                    <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">

                    <x-field name="email" type="email" autocomplete="email" required :model="false"
                             label="Email address" bn="ইমেইল" :value="old('email')"
                             hint="The address on your registration."/>

                    <button type="submit" class="btn btn-primary mt-6 w-full">Email me a reset link</button>
                </form>

                <p class="mt-6 border-t border-ink-900/8 pt-5 text-sm text-ink-500">
                    Remembered it?
                    <a href="{{ route('member.login') }}" class="font-medium text-brass-700 underline underline-offset-4">Back to sign in</a>
                </p>
                <p class="mt-3 text-xs text-ink-400">
                    Still stuck? The registration helpline is
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', config('rcmaa.contact.helpline')) }}"
                       class="text-brass-700 underline underline-offset-4">{{ config('rcmaa.contact.helpline') }}</a>,
                    {{ config('rcmaa.contact.helpline_hours') }}.
                </p>
            </div>
        </div>
    </section>
</x-layout>
