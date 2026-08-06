<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Mail\AlumniAccessLink;
use App\Models\Registration;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

/**
 * Members sign in here with the email and password from their registration.
 *
 * The portal opened as a passwordless, emailed-link affair; the association
 * asked for a password instead. The link route is kept, but only as the way in
 * for anyone who registered before passwords existed — it drops them straight
 * on the "choose a password" screen rather than leaving two ways to sign in
 * indefinitely.
 */
class LoginController extends Controller
{
    private const LINK_MINUTES = 60;

    public function show(): View
    {
        return view('pages.portal.login', [
            'title' => 'Member login',
            'description' => 'Sign in to your RCMAA member account to manage your registration and search the alumni directory.',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:190'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::guard('alumni')->attempt($data, $remember)) {
            // Someone who registered before passwords existed will never get in
            // by guessing. Say so plainly rather than letting them try again.
            $known = Registration::where('email', $data['email'])->first();

            if ($known && ! $known->hasPassword()) {
                throw ValidationException::withMessages([
                    'email' => 'Your registration was made before member accounts existed, so it has no password yet. Use "Forgot password" below and we will email you a link to set one.',
                ]);
            }

            throw ValidationException::withMessages([
                'email' => 'Those details do not match any registration we hold.',
            ]);
        }

        $request->session()->regenerate();

        $member = Auth::guard('alumni')->user();
        $member->forceFill(['last_login_at' => now()])->saveQuietly();

        return redirect()->intended(route('member.dashboard'));
    }

    /**
     * The old emailed link, kept for members with no password.
     *
     * Answers identically whether or not the address is on file — this form
     * must not become a way to test who has registered.
     */
    public function sendLink(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:190'],
            'website' => ['prohibited'], // honeypot
        ], ['website.prohibited' => 'Your request could not be processed.']);

        $registration = Registration::where('email', $data['email'])->first();

        if ($registration) {
            $url = URL::temporarySignedRoute(
                'member.link.open',
                now()->addMinutes(self::LINK_MINUTES),
                ['registration' => $registration->reference]
            );

            try {
                Mail::to($registration->email)->send(new AlumniAccessLink($registration, $url, self::LINK_MINUTES));
            } catch (\Throwable $e) {
                Log::warning('Alumni access link failed to send', [
                    'reference' => $registration->reference,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return back()->with('status', 'If that address is on our register, a secure link is on its way. It is valid for one hour.');
    }

    /** The signed link itself — signs them in and asks them to set a password. */
    public function openLink(Registration $registration): RedirectResponse
    {
        Auth::guard('alumni')->login($registration);
        request()->session()->regenerate();

        $registration->forceFill(['last_login_at' => now()])->saveQuietly();

        if (! $registration->hasPassword()) {
            return redirect()->route('member.password.create')
                ->with('status', 'You are signed in. Choose a password so you can sign in directly next time.');
        }

        return redirect()->route('member.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('alumni')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'You have been signed out.');
    }
}
