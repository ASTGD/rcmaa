<x-layout :title="$title">
    <x-page-hero
        eyebrow="Members"
        title="Choose a new password"
        :breadcrumbs="['Member login' => route('member.login'), 'Reset password' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc max-w-xl">
            <div class="card p-6 md:p-8">
                <form method="POST" action="{{ route('member.password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="grid gap-6">
                        <x-field name="email" type="email" autocomplete="email" required :model="false"
                                 label="Email address" bn="ইমেইল" :value="old('email', $email)"/>

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

                    <button type="submit" class="btn btn-primary mt-7 w-full">Save my new password</button>
                </form>
            </div>
        </div>
    </section>
</x-layout>
