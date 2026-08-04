<x-admin-layout :title="$title">
    <x-slot:actions>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">
            <x-icon name="chevron-left" class="h-3.5 w-3.5"/>Back
        </a>
    </x-slot:actions>

    <form method="POST"
          action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}"
          class="card max-w-xl space-y-5 p-6">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <div>
            <label for="name" class="field-label">Name<span class="text-red-600">*</span></label>
            <input id="name" name="name" type="text" class="input" value="{{ old('name', $user->name) }}">
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="field-label">Email<span class="text-red-600">*</span></label>
            <input id="email" name="email" type="email" class="input" value="{{ old('email', $user->email) }}">
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="field-label">
                    Password @if ($user->exists)<span class="font-normal text-ink-400">(leave blank to keep)</span>@else<span class="text-red-600">*</span>@endif
                </label>
                <input id="password" name="password" type="password" class="input" autocomplete="new-password">
                <p class="field-hint">At least 10 characters.</p>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="field-label">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="input" autocomplete="new-password">
            </div>
        </div>

        <label class="choice !py-2.5 !text-[0.82rem]">
            <input type="checkbox" name="is_admin" value="1" @checked(old('is_admin', $user->is_admin))>
            <span class="choice-box" aria-hidden="true"></span>
            <span>Administrator — can verify payments, see contact details and edit content</span>
        </label>
        @error('is_admin')<p class="field-error">{{ $message }}</p>@enderror

        <button type="submit" class="btn btn-primary">{{ $user->exists ? 'Save Changes' : 'Create Account' }}</button>
    </form>
</x-admin-layout>
