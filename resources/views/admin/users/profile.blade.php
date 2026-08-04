<x-admin-layout :title="$title">
    <form method="POST" action="{{ route('admin.account.update') }}" class="card max-w-xl space-y-5 p-6">
        @csrf @method('PUT')

        <div>
            <label for="name" class="field-label">Name</label>
            <input id="name" name="name" type="text" class="input" value="{{ old('name', $user->name) }}">
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="field-label">Email</label>
            <input id="email" name="email" type="email" class="input" value="{{ old('email', $user->email) }}">
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <hr class="border-ink-900/8">

        <div>
            <label for="current_password" class="field-label">Current password<span class="text-red-600">*</span></label>
            <input id="current_password" name="current_password" type="password" class="input" autocomplete="current-password">
            <p class="field-hint">Required to save any change to this page.</p>
            @error('current_password')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="field-label">New password <span class="font-normal text-ink-400">(optional)</span></label>
                <input id="password" name="password" type="password" class="input" autocomplete="new-password">
                <p class="field-hint">At least 10 characters.</p>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="field-label">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="input" autocomplete="new-password">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</x-admin-layout>
