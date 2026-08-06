<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

/**
 * Forgotten and changed passwords for members.
 *
 * Runs on Laravel's `alumni` password broker rather than a hand-rolled code, so
 * tokens are hashed at rest, single-use and expire on their own.
 */
class PasswordController extends Controller
{
    /** The rule every member password is held to, in one place. */
    public static function rules(): array
    {
        return ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()];
    }

    public function requestForm(): View
    {
        return view('pages.portal.password-request', [
            'title' => 'Forgot your password',
            'description' => 'Reset the password on your RCMAA member account.',
        ]);
    }

    /**
     * Always answers the same way, whether or not the address is on file — this
     * form must not become a way to test who has registered.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email:rfc', 'max:190'],
            'website' => ['prohibited'], // honeypot
        ], ['website.prohibited' => 'Your request could not be processed.']);

        Password::broker('alumni')->sendResetLink($request->only('email'));

        return back()->with('status', 'If that address is on our register, a reset link is on its way. It is valid for one hour.');
    }

    public function resetForm(Request $request, string $token): View
    {
        return view('pages.portal.password-reset', [
            'title' => 'Choose a new password',
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email:rfc'],
            'password' => self::rules(),
        ]);

        $status = Password::broker('alumni')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($member, string $password) {
                $member->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return redirect()->route('member.login')
            ->with('status', 'Your password has been changed. You can sign in with it now.');
    }

    /** For a member signed in by emailed link who has no password yet. */
    public function createForm(): View
    {
        return view('pages.portal.password-create', [
            'title' => 'Set your password',
            'member' => Auth::guard('alumni')->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $member = Auth::guard('alumni')->user();

        // Changing an existing password requires proving you know it; setting a
        // first one does not, because there is nothing to prove.
        $rules = ['password' => self::rules()];

        if ($member->hasPassword()) {
            $rules['current_password'] = ['required', 'current_password:alumni'];
        }

        $request->validate($rules, [
            'current_password.current_password' => 'That is not your current password.',
        ]);

        $member->forceFill(['password' => $request->input('password')])->save();

        return redirect()->route('member.dashboard')
            ->with('status', 'Your password has been saved.');
    }
}
