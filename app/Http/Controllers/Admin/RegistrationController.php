<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Support\RegistrationPricing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.registrations.index', [
            'title' => 'Registrations',
            'registrations' => $this->filtered($request)->paginate(25)->withQueryString(),
            'filters' => $request->only('q', 'status', 'year', 'degree', 'tshirt', 'category'),
            'categories' => RegistrationPricing::categories(),
            'years' => Registration::whereNotNull('passing_year')->distinct()->orderByDesc('passing_year')->pluck('passing_year'),
            'byCategory' => Registration::query()
                ->selectRaw('category, COUNT(*) as total, SUM(amount_paid) as paid')
                ->groupBy('category')
                ->get()
                ->keyBy('category'),
            'counts' => [
                'all' => Registration::count(),
                'pending' => Registration::pending()->count(),
                'verified' => Registration::verified()->count(),
                'rejected' => Registration::where('payment_status', Registration::STATUS_REJECTED)->count(),
            ],
        ]);
    }

    public function show(Registration $registration): View
    {
        return view('admin.registrations.show', [
            'title' => $registration->reference,
            'registration' => $registration->load('verifier'),
        ]);
    }

    /** The correction form the helpdesk needs when a registrant mistypes something. */
    public function edit(Registration $registration): View
    {
        return view('admin.registrations.edit', [
            'title' => 'Edit '.$registration->reference,
            'registration' => $registration,
            'categories' => RegistrationPricing::categories(),
        ]);
    }

    /**
     * Correct a registration on the registrant's behalf.
     *
     * Deliberately separate from the verification action: this changes what the
     * person submitted, so it re-derives the amount due from the (possibly new)
     * category and records what happened in the admin note.
     */
    public function updateDetails(Request $request, Registration $registration): RedirectResponse
    {
        $options = config('rcmaa.options');

        $data = $request->validate([
            'category' => ['required', Rule::in(RegistrationPricing::keys())],
            'full_name_en' => ['required', 'string', 'max:120'],
            'full_name_bn' => ['nullable', 'string', 'max:120'],
            'blood_group' => ['nullable', Rule::in($options['blood_groups'])],
            'mobile' => [
                'required', 'string', 'max:32',
                Rule::unique('registrations', 'mobile')->ignore($registration->id),
            ],
            'whatsapp' => ['nullable', 'string', 'max:32'],
            'email' => [
                'required', 'email:rfc', 'max:190',
                Rule::unique('registrations', 'email')->ignore($registration->id),
            ],
            'present_address' => ['required', 'string', 'max:500'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
            // Canonical list, or whatever this record already holds — a legacy
            // import must stay editable.
            'session' => ['required_unless:category,teacher', 'nullable', Rule::in(array_merge(
                array_keys(config('rcmaa.options.sessions')),
                array_filter([$registration->session])
            ))],
            'masters_session' => ['required_if:degree,both', 'nullable', Rule::in(array_keys(config('rcmaa.options.sessions')))],
            'degree' => ['required_unless:category,teacher', 'nullable', Rule::in(array_keys($options['degrees']))],
            'class_roll' => ['nullable', 'string', 'max:64'],
            'registration_no' => ['nullable', 'string', 'max:64'],
            'passing_year' => [
                'required_unless:category,current_student', 'nullable', 'integer',
                'min:'.config('rcmaa.college_founded'), 'max:'.(date('Y') + 1),
            ],
            'employment_status' => ['required_unless:category,teacher', 'nullable', Rule::in(array_keys($options['employment_statuses']))],
            'profession' => ['nullable', 'string', 'max:120'],
            'designation' => ['nullable', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:180'],
            'tshirt_size' => ['required', Rule::in($options['tshirt_sizes'])],
            'cultural_program' => ['required', 'boolean'],
            'memories' => ['nullable', 'string', 'max:4000'],
            'payment_method' => ['required', Rule::in(array_keys(config('rcmaa.payment.methods')))],
            // Unique across everyone else, so a correction cannot collide.
            'transaction_id' => [
                'required', 'string', 'max:64',
                Rule::unique('registrations')
                    ->where(fn ($q) => $q->where('payment_method', $request->input('payment_method')))
                    ->ignore($registration->id),
            ],
            'sender_number' => ['required', 'string', 'max:32'],
            'amount_paid' => ['required', 'integer', 'min:0', 'max:1000000'],
            'guest_count' => ['required', Rule::in(array_keys($options['guest_counts']))],
        ]);

        // Adjust the guests array to match the updated guest_count
        $guestCountVal = $data['guest_count'];
        if (! RegistrationPricing::allowsGuests($data['category'])) {
            $guestCountVal = '0';
            $data['guest_count'] = '0';
        }

        $numGuests = $guestCountVal === '3+' ? 3 : (int) $guestCountVal;
        $currentGuests = $registration->guests ?? [];
        if (count($currentGuests) > $numGuests) {
            $data['guests'] = array_slice($currentGuests, 0, $numGuests);
        } elseif (count($currentGuests) < $numGuests) {
            $data['guests'] = $currentGuests;
            for ($i = count($currentGuests); $i < $numGuests; $i++) {
                $data['guests'][] = ['name' => 'Guest ' . ($i + 1), 'relation' => 'Spouse/Family', 'occupation' => ''];
            }
        } else {
            $data['guests'] = $currentGuests;
        }

        $data['cultural_program'] = $request->boolean('cultural_program');
        $data['listed_in_directory'] = $request->boolean('listed_in_directory');

        // The category may have changed, so the price must be re-derived.
        $data['category_fee'] = RegistrationPricing::fee($data['category']);
        $data['guest_fee'] = RegistrationPricing::allowsGuests($data['category'])
            ? RegistrationPricing::guestFee()
            : 0;
        $data['amount_due'] = RegistrationPricing::total($data['category'], count($data['guests']));

        if ($data['amount_due'] > $data['amount_paid']) {
            $data['payment_status'] = Registration::STATUS_PENDING;
            $data['verified_at'] = null;
            $data['verified_by'] = null;
        }

        $changed = collect($data)
            ->reject(fn ($v, $k) => $registration->{$k} == $v)
            ->keys()
            ->implode(', ');

        $registration->update($data);

        if ($changed) {
            $registration->update([
                'admin_note' => trim(($registration->admin_note ? $registration->admin_note."\n\n" : '')
                    ."Corrected by {$request->user()->name} on ".now()->format('j M Y, g:i a').": {$changed}."),
            ]);
        }

        return redirect()
            ->route('admin.registrations.show', $registration)
            ->with('status', "Registration {$registration->reference} updated.");
    }

    public function update(Request $request, Registration $registration): RedirectResponse
    {
        $data = $request->validate([
            'payment_status' => ['required', 'in:pending,verified,rejected'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $updateData = [
            ...$data,
            'verified_at' => $data['payment_status'] === Registration::STATUS_VERIFIED ? now() : null,
            'verified_by' => $data['payment_status'] === Registration::STATUS_VERIFIED ? $request->user()->id : null,
        ];

        if ($data['payment_status'] === Registration::STATUS_VERIFIED && $registration->amount_paid < $registration->amount_due) {
            $updateData['amount_paid'] = $registration->amount_due;
        }

        $registration->update($updateData);

        return back()->with('status', "Registration {$registration->reference} marked as {$data['payment_status']}.");
    }

    public function destroy(Registration $registration): RedirectResponse
    {
        if ($registration->photo_path) {
            Storage::disk('public')->delete($registration->photo_path);
        }

        $reference = $registration->reference;
        $registration->delete();

        return redirect()
            ->route('admin.registrations.index')
            ->with('status', "Registration {$reference} deleted.");
    }

    /**
     * One row per registration, as a heading => value map.
     *
     * Deliberately a single associative array rather than parallel header and
     * value lists: those two drifted apart once already and shifted every column
     * after the third, which silently put the wrong figures under "Amount Paid"
     * in the file the committee reconciles money from.
     */
    private function exportRow(Registration $r): array
    {
        return [
            'Reference' => $r->reference,
            'Status' => $r->payment_status,
            'Category' => $r->category_label,
            'Name (English)' => $r->full_name_en,
            'Name (Bangla)' => $r->full_name_bn,
            'Blood Group' => $r->blood_group,
            'Mobile' => $r->mobile,
            'WhatsApp' => $r->whatsapp,
            'Email' => $r->email,
            'Present Address' => $r->present_address,
            'Permanent Address' => $r->permanent_address,
            'Session' => $r->session,
            'Masters Session' => $r->masters_session,
            'Degree' => $r->degree_label,
            'Class Roll' => $r->class_roll,
            'Registration No' => $r->registration_no,
            'Passing Year' => $r->passing_year,
            'Employment' => $r->employment_label,
            'Profession' => $r->profession,
            'Designation' => $r->designation,
            'Organization' => $r->organization,
            'T-Shirt' => $r->tshirt_size,
            'Cultural Program' => $r->cultural_program ? 'Yes' : 'No',
            'Guests' => $r->guest_total,
            'Guest Details' => collect($r->guests ?? [])
                ->map(fn ($g) => trim(($g['name'] ?? '').' ('.($g['relation'] ?? '-').', '.($g['occupation'] ?? '-').')'))
                ->implode('; '),
            'Payment Method' => $r->payment_method_label,
            'Transaction ID' => $r->transaction_id,
            'Sender Number' => $r->sender_number,
            'Receipt' => $r->payment_receipt_path ? 'Attached' : '',
            'Category Fee' => $r->category_fee,
            'Guest Fee (each)' => $r->guest_fee,
            'Amount Due' => $r->amount_due,
            'Amount Paid' => $r->amount_paid,
            'Balance' => $r->balance,
            'Memories' => $r->memories,
            'Registered At' => $r->created_at?->format('Y-m-d H:i'),
            'Verified At' => $r->verified_at?->format('Y-m-d H:i'),
            'Verified By' => $r->verifier?->name,
        ];
    }

    /** Streamed so a few thousand rows never hold the whole file in memory. */
    public function export(Request $request): StreamedResponse
    {
        $filename = 'rcmaa-registrations-'.now()->format('Y-m-d-Hi').'.csv';
        $query = $this->filtered($request)->with('verifier');

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            // BOM so Excel opens the Bangla columns as UTF-8.
            fwrite($handle, "\xEF\xBB\xBF");

            $headerWritten = false;

            $query->chunk(200, function ($rows) use ($handle, &$headerWritten) {
                foreach ($rows as $r) {
                    $row = $this->exportRow($r);

                    if (! $headerWritten) {
                        fputcsv($handle, array_keys($row));
                        $headerWritten = true;
                    }

                    fputcsv($handle, array_values($row));
                }
            });

            // An empty result set should still produce a usable file.
            if (! $headerWritten) {
                fputcsv($handle, array_keys($this->exportRow(new Registration)));
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function filtered(Request $request)
    {
        return Registration::query()
            ->search($request->string('q')->toString())
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('payment_status', $s))
            ->when($request->integer('year'), fn ($q, $y) => $q->where('passing_year', $y))
            ->when($request->string('degree')->toString(), fn ($q, $d) => $q->where('degree', $d))
            ->when($request->string('tshirt')->toString(), fn ($q, $t) => $q->where('tshirt_size', $t))
            ->when($request->string('category')->toString(), fn ($q, $c) => $q->where('category', $c))
            ->latest();
    }
}
