<?php

namespace App\Http\Controllers;

use App\Mail\AlumniAccessLink;
use App\Models\Registration;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;

/**
 * The registrant's own area, reached by a one-time emailed link.
 *
 * Deliberately password-free. The association is run by volunteers, and every
 * password is a reset request somebody has to field; a signed, expiring link
 * costs them nothing and there is no credential to leak. The link is the
 * capability, so it expires in an hour and the session it opens is cleared on
 * sign-out.
 */
class AlumniPortalController extends Controller
{
    /** Public so the directory gate can look for the same session. */
    public const SESSION_KEY = 'alumni_registration_id';

    private const LINK_MINUTES = 60;

    public function request(): View
    {
        return view('pages.portal.request', [
            'title' => 'Manage your registration',
            'description' => 'Get a secure link to view and update your reunion registration.',
        ]);
    }

    /**
     * Always answers the same way, whether or not the address is on file — the
     * form must not become a way to test who has registered.
     */
    public function sendLink(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:190'],
            'website' => ['prohibited'], // honeypot
        ], ['website.prohibited' => 'Your request could not be processed.']);

        $registration = Registration::where('email', $data['email'])->latest()->first();

        if ($registration) {
            $url = URL::temporarySignedRoute(
                'portal.open',
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

    /** The signed link itself — exchanges the URL for a session. */
    public function open(Registration $registration): RedirectResponse
    {
        session([self::SESSION_KEY => $registration->id]);

        return redirect()->route('portal.show');
    }

    public function show(Request $request): View
    {
        return view('pages.portal.show', [
            'title' => 'Your registration',
            'registration' => $this->current($request),
        ]);
    }

    /**
     * What a registrant may change about themselves.
     *
     * Contact details, reunion preferences and directory visibility only.
     * Deliberately excluded: name, session, degree and passing year (identity
     * the committee verifies against), the category and every payment field
     * (money), and the email address itself — changing that from a link sent to
     * it is a hijack waiting to happen.
     */
    public function update(Request $request): RedirectResponse
    {
        $registration = $this->current($request);
        $options = config('rcmaa.options');

        $data = $request->validate([
            'mobile' => ['required', 'string', 'max:32', 'regex:/^(\+?88)?01[3-9]\d{8}$/'],
            'whatsapp' => ['nullable', 'string', 'max:32'],
            'blood_group' => ['nullable', Rule::in($options['blood_groups'])],
            'present_address' => ['required', 'string', 'max:500'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
            'profession' => ['nullable', 'string', 'max:120'],
            'designation' => ['nullable', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:180'],
            'tshirt_size' => ['required', Rule::in($options['tshirt_sizes'])],
            'memories' => ['nullable', 'string', 'max:4000'],
        ], [
            'mobile.regex' => 'Enter a valid Bangladeshi mobile number, e.g. 01712345678.',
        ]);

        $registration->update([
            ...$data,
            'listed_in_directory' => $request->boolean('listed_in_directory'),
        ]);

        return back()->with('status', 'Your details have been updated.');
    }

    /**
     * Attaching the payment receipt after the fact.
     *
     * Kept apart from update() so it is plain that a file arriving here can
     * only ever write the receipt path — never the amount, never the status.
     * This is the recovery route for anyone who registered before the upload
     * existed, or who could not find the SMS at the time.
     */
    public function uploadReceipt(Request $request): RedirectResponse
    {
        $registration = $this->current($request);

        $request->validate([
            'payment_receipt' => [
                'required', 'file', 'mimes:jpg,jpeg,png,webp,pdf',
                'max:'.config('rcmaa.registration.receipt_max_kb'),
            ],
        ], [
            'payment_receipt.required' => 'Choose a file to upload.',
            'payment_receipt.mimes' => 'The receipt must be a JPG, PNG, WebP or PDF file.',
            'payment_receipt.max' => 'The receipt must not be larger than 4 MB.',
        ]);

        // Replacing an earlier attempt should not orphan the old file.
        if ($registration->payment_receipt_path) {
            Storage::disk('public')->delete($registration->payment_receipt_path);
        }

        $registration->update([
            'payment_receipt_path' => $request->file('payment_receipt')
                ->store('registrations/receipts', 'public'),
        ]);

        return back()->with('status', 'Your receipt has been attached. The committee will check it shortly.');
    }

    /** A print-ready pass for the registration desk. */
    public function pass(Request $request): View
    {
        return view('pages.portal.pass', [
            'title' => 'Reunion pass',
            'registration' => $this->current($request),
        ]);
    }

    public function close(Request $request): RedirectResponse
    {
        $request->session()->forget(self::SESSION_KEY);

        return redirect()->route('home')->with('status', 'You have been signed out of your registration.');
    }

    private function current(Request $request): Registration
    {
        $id = $request->session()->get(self::SESSION_KEY);

        abort_unless($id, 403);

        return Registration::findOrFail($id);
    }
}
