<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact', [
            'title' => 'Contact Us',
            'description' => 'Reach the RCMAA committee and reunion helpdesk.',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:32'],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'min:20', 'max:4000'],
            // Honeypot — real users never see or fill this.
            'website' => ['prohibited'],
        ], [
            'website.prohibited' => 'Your submission could not be processed.',
        ]);

        unset($data['website']);

        ContactMessage::create([...$data, 'ip_address' => $request->ip()]);

        return back()->with('status', 'Thank you — your message has reached the RCMAA committee. We usually reply within two working days.');
    }
}
