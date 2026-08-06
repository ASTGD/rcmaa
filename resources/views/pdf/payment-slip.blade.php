{{--
    The payment slip a member downloads from their dashboard.

    A record of what was charged and what was paid. It is deliberately not
    called a receipt while payment is unverified — the committee confirms
    against bKash, and this document must not appear to do that for them.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment slip {{ $r->reference }}</title>
    @include('pdf.partials.style')
</head>
<body>
    @include('pdf.partials.head', ['kind' => 'Payment Slip'])

    @php
        $status = match ($r->payment_status) {
            'verified' => ['Verified by the committee', 'chip-ok'],
            'rejected' => ['Not verified', 'chip-no'],
            default => ['Awaiting verification', 'chip-wait'],
        };
        $outstanding = max(0, (int) $r->amount_due - (int) $r->amount_paid);
    @endphp

    <table class="data">
        <tr><td class="k">Name</td><td class="v">{{ $r->full_name_en }}</td></tr>
        <tr><td class="k">Category</td><td class="v">{{ $r->category_label }}</td></tr>
        <tr><td class="k">Session</td><td class="v">{{ $r->session ?: '—' }}</td></tr>
        <tr><td class="k">Status</td><td class="v"><span class="chip {{ $status[1] }}">{{ $status[0] }}</span></td></tr>
        @if ($r->verified_at)
            <tr><td class="k">Verified on</td><td class="v">{{ $r->verified_at->format('j F Y') }}</td></tr>
        @endif
    </table>

    <h2>Charges</h2>
    <table class="data">
        <tr><td class="k">Registration fee</td><td class="v">BDT {{ number_format($r->category_fee) }}</td></tr>
        @if ($r->guest_fee)
            <tr><td class="k">Guests ({{ $r->guest_total }})</td><td class="v">BDT {{ number_format($r->guest_fee) }}</td></tr>
        @endif
        <tr><td class="k">Total due</td><td class="v">BDT {{ number_format($r->amount_due) }}</td></tr>
    </table>

    <div class="total">
        <table width="100%">
            <tr>
                <td>Amount paid</td>
                <td align="right" class="amt">BDT {{ number_format($r->amount_paid) }}</td>
            </tr>
            @if ($outstanding > 0)
                <tr>
                    <td style="padding-top:5px; color:#8f2020;">Still outstanding</td>
                    <td align="right" style="padding-top:5px; color:#8f2020; font-weight:bold;">
                        BDT {{ number_format($outstanding) }}
                    </td>
                </tr>
            @endif
        </table>
    </div>

    <h2>How it was paid</h2>
    <table class="data">
        <tr><td class="k">Method</td><td class="v">{{ $r->payment_method_label ?? Str::title($r->payment_method) }}</td></tr>
        <tr><td class="k">Transaction ID</td><td class="v">{{ $r->transaction_id }}</td></tr>
        <tr><td class="k">Sender number</td><td class="v">{{ $r->sender_number }}</td></tr>
        <tr><td class="k">Receipt attached</td><td class="v">{{ $r->payment_receipt_path ? 'Yes' : 'No' }}</td></tr>
    </table>

    @if ($r->payment_status !== 'verified')
        <div class="note">
            This slip records what you told us you paid. The committee checks each payment
            against the bKash account before confirming it, so it is not proof of verified
            payment on its own. You will see the status change in your account once checked.
        </div>
    @endif

    <div class="foot">
        {{ config('rcmaa.name') }} &middot; Generated {{ now()->format('j F Y, g:ia') }} &middot;
        Helpline {{ config('rcmaa.contact.helpline') }}
    </div>
</body>
</html>
