<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Mail\RegistrationReceived;
use App\Models\Registration;
use App\Support\RegistrationPricing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('pages.register', [
            'title' => 'Reunion Registration',
            'description' => 'Register for the Rajshahi College Mathematics Grand Reunion 2026.',
        ]);
    }

    public function store(RegistrationRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['photo', 'payment_receipt', 'terms', 'website']);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('registrations/photos', 'public');
        }

        if ($request->hasFile('payment_receipt')) {
            $data['payment_receipt_path'] = $request->file('payment_receipt')
                ->store('registrations/receipts', 'public');
        }

        $data['amount_due'] = $request->expectedFee();
        // Snapshot the prices actually applied, so a later fee change cannot
        // rewrite what somebody was charged.
        $data['category_fee'] = RegistrationPricing::fee($data['category']);
        $data['guest_fee'] = RegistrationPricing::allowsGuests($data['category'])
            ? RegistrationPricing::guestFee()
            : 0;
        $data['ip_address'] = $request->ip();

        $registration = Registration::create($data);

        // They just chose a password; signing them in means the confirmation
        // page's "manage my registration" works there and then, rather than
        // bouncing them to a login form they filled the credentials for a
        // moment ago. The `hashed` cast has already hashed what they typed.
        Auth::guard('alumni')->login($registration);

        // A failed confirmation email must not lose us the registration itself.
        try {
            Mail::to($registration->email)->send(new RegistrationReceived($registration));
        } catch (\Throwable $e) {
            Log::warning('Registration confirmation email failed', [
                'reference' => $registration->reference,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('register.confirmation', $registration->reference)
            ->with('just_registered', true);
    }

    public function confirmation(Registration $registration): View
    {
        return view('pages.register-confirmation', [
            'title' => 'Registration Received',
            'registration' => $registration,
        ]);
    }

    public function statusForm(): View
    {
        return view('pages.registration-status', [
            'title' => 'Check Registration Status',
            'description' => 'Look up your reunion registration using your reference number.',
            'registration' => null,
        ]);
    }

    public function statusLookup(Request $request): View
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:32'],
            'mobile' => ['required', 'string', 'max:32'],
        ]);

        // Reference alone would let anyone enumerate entries; pairing it with the
        // mobile number keeps the lookup to the person who registered.
        $registration = Registration::where('reference', strtoupper(trim($data['reference'])))
            ->where('mobile', preg_replace('/[\s-]/', '', $data['mobile']))
            ->first();

        return view('pages.registration-status', [
            'title' => 'Check Registration Status',
            'registration' => $registration,
            'searched' => true,
        ]);
    }
}
