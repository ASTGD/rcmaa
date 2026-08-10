<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'donor_name' => ['required', 'string', 'max:120'],
            'phone_number' => ['required', 'string', 'max:32'],
            'amount' => ['required', 'numeric', 'min:10', 'max:99999999'],
            'transaction_id' => ['required', 'string', 'max:120'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:4096'],
        ], [
            'donor_name.required' => 'Please enter the donor name.',
            'phone_number.required' => 'Please enter a contact phone number.',
            'amount.required' => 'Please specify the donation amount.',
            'amount.min' => 'Minimum donation amount is BDT 10.',
            'transaction_id.required' => 'Please enter the transaction reference number.',
            'receipt.mimes' => 'The receipt must be a JPG, JPEG, PNG, WebP or PDF file.',
            'receipt.max' => 'The receipt must not be larger than 4 MB.',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('donations/receipts', 'public');
        }

        Donation::create([
            'donor_name' => $data['donor_name'],
            'phone_number' => $data['phone_number'],
            'amount' => $data['amount'],
            'transaction_id' => $data['transaction_id'],
            'receipt_path' => $receiptPath,
            'is_verified' => false,
        ]);

        return back()->with('donation_status', 'Thank you! Your donation details have been submitted. The committee will verify and confirm it shortly.');
    }
}
