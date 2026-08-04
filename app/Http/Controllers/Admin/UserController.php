<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Committee accounts.
 *
 * The reunion is run by a sub-committee, not one person, so the seeded account
 * cannot be the only way in. Two rules are enforced throughout: nobody may strip
 * their own administrator rights, and the last remaining administrator cannot be
 * deleted or demoted — otherwise the admin locks everyone out permanently.
 */
class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'title' => 'Committee accounts',
            'users' => User::orderByDesc('is_admin')->orderBy('name')->paginate(25),
            'adminCount' => User::where('is_admin', true)->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', ['title' => 'New account', 'user' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(10)],
        ]);

        User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Account created.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['title' => 'Edit '.$user->name, 'user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(10)],
        ]);

        $isAdmin = $request->boolean('is_admin');

        // Guard against locking the association out of its own admin.
        if ($user->is_admin && ! $isAdmin) {
            if ($user->id === $request->user()->id) {
                return back()->withErrors(['is_admin' => 'You cannot remove your own administrator access.']);
            }

            if (User::where('is_admin', true)->count() <= 1) {
                return back()->withErrors(['is_admin' => 'This is the last administrator — promote somebody else first.']);
            }
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_admin' => $isAdmin,
            ...(filled($data['password'] ?? null) ? ['password' => Hash::make($data['password'])] : []),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Account updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return back()->withErrors(['user' => 'This is the last administrator and cannot be deleted.']);
        }

        $user->delete();

        return back()->with('status', 'Account deleted.');
    }

    // --- The signed-in user's own account -----------------------------------

    public function profile(Request $request): View
    {
        return view('admin.users.profile', ['title' => 'Your account', 'user' => $request->user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190', Rule::unique('users', 'email')->ignore($request->user()->id)],
            'current_password' => ['required', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(10)],
        ], [
            'current_password.current_password' => 'That is not your current password.',
        ]);

        $request->user()->update([
            'name' => $data['name'],
            'email' => $data['email'],
            ...(filled($data['password'] ?? null) ? ['password' => Hash::make($data['password'])] : []),
        ]);

        return back()->with('status', filled($data['password'] ?? null)
            ? 'Your details and password were updated.'
            : 'Your details were updated.');
    }
}
