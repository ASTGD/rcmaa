@if (session('donation_status'))
    <div class="mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-500/25 text-emerald-800 text-sm font-medium">
        {{ session('donation_status') }}
    </div>
@else
    <div class="mt-6 border-t border-ink-900/8 pt-6">
        <p class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-brass-700 mb-4">
            Submit Donation Details <span lang="bn" class="font-bangla normal-case tracking-normal">&middot; ডোনেশনের বিবরণ সাবমিট করুন</span>
        </p>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="min-w-0">
                <label class="field-label" for="reg-don-name">Donor Name <span class="text-red-600">*</span></label>
                <input form="donation-submit-form" id="reg-don-name" name="donor_name" type="text" class="input mt-1.5" placeholder="e.g. Abul Kalam" required value="{{ old('donor_name') }}">
                @error('donor_name') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div class="min-w-0">
                <label class="field-label" for="reg-don-phone">Phone Number <span class="text-red-600">*</span></label>
                <input form="donation-submit-form" id="reg-don-phone" name="phone_number" type="tel" class="input mt-1.5" placeholder="e.g. 01712345678" required value="{{ old('phone_number') }}">
                @error('phone_number') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div class="min-w-0">
                <label class="field-label" for="reg-don-amount">Donation Amount (BDT) <span class="text-red-600">*</span></label>
                <input form="donation-submit-form" id="reg-don-amount" name="amount" type="number" class="input mt-1.5" placeholder="e.g. 5000" required value="{{ old('amount') }}">
                @error('amount') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div class="min-w-0">
                <label class="field-label" for="reg-don-txid">Transaction ID / Ref <span class="text-red-600">*</span></label>
                <input form="donation-submit-form" id="reg-don-txid" name="transaction_id" type="text" class="input mt-1.5" placeholder="e.g. TR0X123456" required value="{{ old('transaction_id') }}">
                @error('transaction_id') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div class="min-w-0 sm:col-span-2">
                <label class="field-label" for="reg-don-receipt">Payment Receipt / Screenshot <span class="text-ink-400 font-normal">(Optional)</span></label>
                <input form="donation-submit-form" id="reg-don-receipt" name="receipt" type="file" class="input mt-1.5" accept="image/*,application/pdf">
                @error('receipt') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>
        <button form="donation-submit-form" type="submit" class="btn btn-primary btn-sm mt-5">
            Submit Donation
            <x-icon name="arrow-right" class="h-3.5 w-3.5"/>
        </button>
    </div>
@endif
