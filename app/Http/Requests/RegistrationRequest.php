<?php

namespace App\Http\Requests;

use App\Http\Controllers\Member\PasswordController;
use App\Support\PaymentMethods;
use App\Support\RegistrationPricing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) config('rcmaa.registration.open');
    }

    public function rules(): array
    {
        $options = config('rcmaa.options');
        $currentYear = (int) date('Y');

        return [
            // Which price band the registrant falls into — drives the fee and
            // whether guests are permitted at all.
            'category' => ['required', Rule::in(RegistrationPricing::keys())],

            // Part 1 — Personal
            'full_name_en' => ['required', 'string', 'max:120'],
            'full_name_bn' => ['nullable', 'string', 'max:120'],
            'blood_group' => ['nullable', Rule::in($options['blood_groups'])],
            'mobile' => ['required', 'string', 'max:32', 'regex:/^(\+?88)?01[3-9]\d{8}$/'],
            'whatsapp' => ['nullable', 'string', 'max:32', 'regex:/^(\+?\d{1,3})?[\d\s-]{6,18}$/'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'profession_type' => ['nullable', Rule::in(array_keys($options['profession_types']))],
            'work_location' => ['nullable', Rule::in(array_keys(config('bd-geo')))],
            'teacher_type' => ['required_if:category,teacher', 'nullable', Rule::in(array_keys($options['teacher_types']))],
            // Deliberately not `dns` — it makes a submission depend on a live DNS
            // lookup and rejects otherwise-valid domains that publish no MX record.
            // Unique because it is now the thing they sign in with, and one
            // address has to mean one member.
            'email' => ['required', 'email:rfc', 'max:190', Rule::unique('registrations', 'email')],

            // The member account's password, chosen during registration at the
            // association's request.
            'password' => PasswordController::rules(),

            'present_address' => ['required', 'string', 'max:500'],
            'present_district' => ['required', Rule::in(array_keys(config('bd-geo')))],
            'present_upazila' => ['required', $this->upazilaBelongsTo('present_district')],
            'permanent_address' => ['nullable', 'string', 'max:500'],
            'permanent_district' => ['nullable', Rule::in(array_keys(config('bd-geo')))],
            'permanent_upazila' => ['nullable', $this->upazilaBelongsTo('permanent_district')],

            // Part 2 — Academic
            // Teachers are staff, not graduates of the department — they are never
            // shown the academic or professional steps, so nothing there is required
            // of them.
            'session' => ['required_unless:category,teacher', 'nullable', Rule::in(array_keys(config('rcmaa.options.sessions')))],
            // Only somebody who did both degrees here has a second session.
            'masters_session' => ['required_if:degree,both', 'nullable', Rule::in(array_keys(config('rcmaa.options.sessions')))],
            'degree' => ['required_unless:category,teacher', 'nullable', Rule::in(array_keys($options['degrees']))],
            'class_roll' => ['nullable', 'string', 'max:64'],
            'registration_no' => ['nullable', 'string', 'max:64'],
            // Current students have not passed yet, so there is nothing to give.
            'passing_year' => [
                // Neither a current student nor a teacher has a passing year to give.
                'required_unless:category,current_student,teacher', 'nullable', 'integer',
                'min:'.config('rcmaa.college_founded'), 'max:'.($currentYear + 1),
            ],

            // Part 3 — Professional
            'employment_status' => ['required_unless:category,teacher', 'nullable', Rule::in(array_keys($options['employment_statuses']))],
            'profession' => ['nullable', 'required_if:employment_status,employed,self_employed', 'string', 'max:120'],
            'designation' => ['nullable', 'string', 'max:120'],
            'organization' => ['nullable', 'required_if:employment_status,employed,self_employed', 'string', 'max:180'],

            // Part 4 — Reunion & event
            'tshirt_size' => ['required', Rule::in($options['tshirt_sizes'])],
            'cultural_program' => ['required', 'boolean'],
            'guest_count' => ['required', Rule::in(array_keys($options['guest_counts']))],
            'guests' => ['array', 'max:8'],
            'guests.*.name' => ['required', 'string', 'max:120'],
            'guests.*.relation' => ['nullable', 'string', 'max:60'],
            'guests.*.occupation' => ['nullable', 'string', 'max:120'],

            // Part 5 — Memories. Capped at 180 characters at the association's
            // request (they asked for 150–180; the ceiling is the limit).
            'memories' => ['nullable', 'string', 'max:180'],

            // Part 6 — Photo. Required since 8 Aug 2026: it goes on the reunion
            // identity card, and chasing missing photos by phone did not scale.
            'photo' => [
                'required', 'image', 'mimes:jpg,jpeg,png,webp',
                'max:'.config('rcmaa.registration.photo_max_kb'),
                'dimensions:min_width=200,min_height=200',
            ],

            // Part 7 — Payment
            // Only what the page is actually offering; a method whose account
            // is not ready must not be acceptable to the server either.
            'payment_method' => ['required', Rule::in(PaymentMethods::keys())],
            'transaction_id' => [
                'required', 'string', 'max:64', 'alpha_num',
                Rule::unique('registrations')->where(
                    fn ($query) => $query->where('payment_method', $this->input('payment_method'))
                ),
            ],
            'sender_number' => ['required', 'string', 'max:32'],
            'payment_receipt' => [
                'nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf',
                'max:'.config('rcmaa.registration.receipt_max_kb'),
            ],
            'amount_paid' => ['required', 'integer', 'min:1', 'max:1000000'],
            'terms' => ['accepted'],

            'website' => ['prohibited'], // honeypot
        ];
    }

    /**
     * An upazila is only valid within its own district — "Savar" under Rajshahi
     * is a data-entry accident the directory would then filter on.
     */
    private function upazilaBelongsTo(string $districtField): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($districtField) {
            $upazilas = config('bd-geo')[$this->input($districtField)] ?? [];

            if (! in_array($value, $upazilas, true)) {
                $fail('Choose an upazila or thana from the selected district.');
            }
        };
    }

    public function messages(): array
    {
        return [
            'mobile.regex' => 'Enter a valid Bangladeshi mobile number, e.g. 01712345678.',
            'present_district.required' => 'Please choose your district.',
            'present_upazila.required' => 'Please choose your upazila or thana.',
            'memories.max' => 'Please keep your memory within 180 characters.',
            'photo.required' => 'Please upload a passport-size photograph — it is printed on your reunion identity card.',
            'email.unique' => 'A registration already exists for this email address. Sign in to your member account instead, or use "Forgot password" if you cannot remember it.',
            'password.confirmed' => 'The two passwords do not match.',
            'transaction_id.unique' => 'This transaction ID has already been used for a registration. If you believe this is an error, contact the helpdesk.',
            'terms.accepted' => 'Please confirm that the information you provided is correct.',
            'passing_year.required_unless' => 'Please give the year you passed.',
            'session.required_unless' => 'Please choose your session.',
            'degree.required_unless' => 'Please choose the degree you completed here.',
            'employment_status.required_unless' => 'Please tell us your current status.',
            'masters_session.required_if' => 'Please choose your Masters session as well.',
            'photo.max' => 'The passport photo must not be larger than 1 MB.',
            'photo.dimensions' => 'The photo is too small — please upload an image at least 200×200 pixels.',
            'payment_receipt.mimes' => 'The receipt must be a JPG, PNG, WebP or PDF file.',
            'payment_receipt.max' => 'The receipt must not be larger than 4 MB.',
            'website.prohibited' => 'Your submission could not be processed.',
            'guests.*.name.required' => 'Please provide a name for each accompanying guest.',
            'profession.required_if' => 'Please tell us your profession or sector.',
            'organization.required_if' => 'Please tell us where you work.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'mobile' => preg_replace('/[\s-]/', '', (string) $this->input('mobile')),
            'cultural_program' => filter_var($this->input('cultural_program'), FILTER_VALIDATE_BOOL),
            // Drop rows the repeater left blank so an empty extra slot isn't an error.
            'guests' => collect($this->input('guests', []))
                ->filter(fn ($guest) => filled($guest['name'] ?? null))
                ->values()
                ->all(),
        ]);
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                // Guests declared vs guests actually supplied must agree, otherwise
                // the fee we charge and the seats we reserve drift apart.
                $category = $this->input('category');
                $declared = $this->input('guest_count');
                $supplied = count($this->input('guests', []));

                // Categories 3 and 4 are individual registrations only.
                if (! RegistrationPricing::allowsGuests($category) && ($supplied > 0 || $declared !== '0')) {
                    $validator->errors()->add(
                        'guest_count',
                        'The '.RegistrationPricing::label($category).' category cannot include accompanying guests.'
                    );

                    return;
                }

                if ($declared !== '3+' && (int) $declared !== $supplied) {
                    $validator->errors()->add(
                        'guests',
                        "You selected {$declared} guest(s) but provided details for {$supplied}."
                    );
                }

                if ($declared === '3+' && $supplied < 3) {
                    $validator->errors()->add('guests', 'Please provide details for at least three guests.');
                }

                // Underpayment is a hard stop; overpayment is allowed and refunded
                // at the desk, so it only fails when short.
                $expected = $this->expectedFee($supplied);

                if ((int) $this->input('amount_paid') < $expected) {
                    $validator->errors()->add(
                        'amount_paid',
                        "The total for you plus {$supplied} guest(s) is BDT ".number_format($expected).
                        '. Please pay the full amount and enter it here.'
                    );
                }
            },
        ];
    }

    public function expectedFee(?int $guests = null): int
    {
        $guests ??= count($this->input('guests', []));

        return RegistrationPricing::total($this->input('category'), $guests);
    }
}
