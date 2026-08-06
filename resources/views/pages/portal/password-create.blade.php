{{-- Setting a first password, or changing an existing one. --}}
<x-layout :title="$title">
    <x-page-hero
        eyebrow="Your account"
        :title="$member->hasPassword() ? 'Change your password' : 'Set your password'"
        :breadcrumbs="['Your account' => route('member.dashboard'), 'Password' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc max-w-xl">
            <div class="card p-6 md:p-8">
                @if (session('status'))
                    <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
                @endif

                @unless ($member->hasPassword())
                    <x-alert type="info" class="mb-6">
                        Your registration was made before member accounts existed. Choose a password
                        and you can sign in directly from now on, without waiting for an email.
                    </x-alert>
                @endunless

                <form method="POST" action="{{ route('member.password.store') }}">
                    @csrf

                    <div class="grid gap-6">
                        @if ($member->hasPassword())
                            <div>
                                <label for="current_password" class="field-label">
                                    Current password <span class="text-brass-700">*</span>
                                </label>
                                <input id="current_password" name="current_password" type="password" required
                                       autocomplete="current-password" class="input mt-2">
                                @error('current_password')<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                        @endif

                        <div>
                            <label for="password" class="field-label">
                                New password <span lang="bn" class="field-label-bn">&middot; নতুন পাসওয়ার্ড</span>
                                <span class="text-brass-700">*</span>
                            </label>
                            <input id="password" name="password" type="password" required
                                   autocomplete="new-password" class="input mt-2">
                            <p class="mt-1.5 text-xs text-ink-400">At least 8 characters, with letters and numbers.</p>
                            @error('password')<p class="field-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="field-label">
                                Confirm new password <span class="text-brass-700">*</span>
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                   autocomplete="new-password" class="input mt-2">
                        </div>
                    </div>

                    <div class="mt-7 flex flex-wrap gap-3">
                        <button type="submit" class="btn btn-primary">Save password</button>
                        <a href="{{ route('member.dashboard') }}" class="btn btn-outline">Back to my account</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layout>
