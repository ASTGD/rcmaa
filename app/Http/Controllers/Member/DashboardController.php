<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * The member's own area, once signed in.
 *
 * What a member may change about themselves is deliberately narrower than what
 * the form collected. Contact details, workplace, reunion preferences, their
 * photo and their directory listing are theirs. The session, degree, passing
 * year, category and every payment field are not — the committee verifies
 * against those, and a member editing them after verification would quietly
 * invalidate the check. Name is editable because the association asked for it.
 */
class DashboardController extends Controller
{
    public function show(Request $request): View
    {
        return view('pages.portal.dashboard', [
            'title' => 'Your account',
            'registration' => $this->member(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $member = $this->member();
        $options = config('rcmaa.options');

        $data = $request->validate([
            'full_name_en' => ['required', 'string', 'max:120'],
            'full_name_bn' => ['nullable', 'string', 'max:120'],
            'mobile' => ['required', 'string', 'max:32', 'regex:/^(\+?88)?01[3-9]\d{8}$/'],
            'whatsapp' => ['nullable', 'string', 'max:32'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'profession_type' => ['nullable', Rule::in(array_keys($options['profession_types']))],
            'work_location' => ['nullable', Rule::in(array_keys(config('bd-geo')))],
            'teacher_type' => [$member->category === 'teacher' ? 'required' : 'nullable', Rule::in(array_keys($options['teacher_types']))],
            'blood_group' => ['nullable', Rule::in($options['blood_groups'])],
            'present_address' => ['required', 'string', 'max:500'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
            'employment_status' => ['nullable', Rule::in(array_keys($options['employment_statuses']))],
            'profession' => ['nullable', 'string', 'max:120'],
            'designation' => ['nullable', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:180'],
            'tshirt_size' => ['required', Rule::in($options['tshirt_sizes'])],
            'memories' => ['nullable', 'string', 'max:4000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.config('rcmaa.registration.photo_max_kb', 3072)],
        ], [
            'mobile.regex' => 'Enter a valid Bangladeshi mobile number, e.g. 01712345678.',
            'photo.image' => 'Your profile picture must be an image.',
            'photo.max' => 'Your profile picture is too large.',
        ]);

        if ($request->hasFile('photo')) {
            // Replacing an earlier picture should not orphan the old file.
            if ($member->photo_path) {
                Storage::disk('public')->delete($member->photo_path);
            }

            $data['photo_path'] = $request->file('photo')->store('registrations/photos', 'public');
        }

        unset($data['photo']);

        $member->update([
            ...$data,
            'listed_in_directory' => $request->boolean('listed_in_directory'),
        ]);

        return redirect()->route('member.dashboard')->with('status', 'Your profile has been updated.');
    }

    /**
     * Attaching the payment receipt after the fact.
     *
     * Kept apart from update() so it is plain that a file arriving here can only
     * ever write the receipt path — never the amount, never the status.
     */
    public function uploadReceipt(Request $request): RedirectResponse
    {
        $member = $this->member();

        $request->validate([
            'payment_receipt' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:'.config('rcmaa.registration.receipt_max_kb'),
            ],
        ], [
            'payment_receipt.required' => 'Choose a file to upload.',
            'payment_receipt.mimes' => 'The receipt must be a JPG, PNG, WebP or PDF file.',
            'payment_receipt.max' => 'The receipt must not be larger than 4 MB.',
        ]);

        if ($member->payment_receipt_path) {
            Storage::disk('public')->delete($member->payment_receipt_path);
        }

        $member->update([
            'payment_receipt_path' => $request->file('payment_receipt')
                ->store('registrations/receipts', 'public'),
        ]);

        return back()->with('status', 'Your receipt has been attached. The committee will check it shortly.');
    }

    /** The registration confirmation slip, as a PDF. */
    public function registrationSlip(): Response
    {
        $member = $this->member();

        return $this->pdf('pdf.registration-slip', $member, "RCMAA-registration-{$member->reference}.pdf");
    }

    /** The payment slip, as a PDF. */
    public function paymentSlip(): Response
    {
        $member = $this->member();

        if ($member->payment_status !== Registration::STATUS_VERIFIED) {
            abort(403, 'Your payment is not yet verified.');
        }

        return $this->pdf('pdf.payment-slip', $member, "RCMAA-payment-{$member->reference}.pdf");
    }

    /** A print-ready pass for the registration desk. */
    public function pass(): Response
    {
        $member = $this->member();

        if ($member->payment_status !== Registration::STATUS_VERIFIED) {
            abort(403, 'Your payment is not yet verified.');
        }

        return $this->pdf('pdf.pass', $member, "RCMAA-pass-{$member->reference}.pdf");
    }

    private function pdf(string $view, Registration $member, string $filename): Response
    {
        $pdf = Pdf::loadView($view, [
            'r' => $member,
            // dompdf cannot fetch over the network, so anything shown in the PDF
            // has to be handed to it as a local path or a data URI.
            'logo' => $this->dataUri(public_path('media/logo.png')),
            'photo' => $member->photo_path ? $this->dataUri(Storage::disk('public')->path($member->photo_path)) : null,
        ])->setPaper('a4');

        return $pdf->download($filename);
    }

    /** dompdf renders images from data URIs reliably; from URLs it does not. */
    private function dataUri(string $path): ?string
    {
        if (! is_file($path)) {
            return null;
        }

        $mime = match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            default => null,
        };

        return $mime ? 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path)) : null;
    }

    private function member(): Registration
    {
        return Auth::guard('alumni')->user();
    }
}
