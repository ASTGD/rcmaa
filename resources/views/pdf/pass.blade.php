<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Entry Pass {{ $r->reference }}</title>
    @include('pdf.partials.style')
    <style>
        .pass-card {
            border: 1px solid #c8a863;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 20px;
            background: #ffffff;
        }
        .pass-header {
            background: #0c1220;
            color: #ffffff;
            padding: 15px;
        }
        .pass-body {
            padding: 20px;
        }
        .pass-photo {
            width: 100px;
            height: 125px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid #e3e0d8;
        }
        .pass-no-photo {
            width: 100px;
            height: 125px;
            border-radius: 6px;
            border: 1px solid #e3e0d8;
            background: #f7f5ef;
            text-align: center;
            line-height: 125px;
            font-size: 32px;
            color: #c8a863;
            font-weight: bold;
        }
        .pass-details {
            vertical-align: top;
            padding-left: 20px;
        }
        .pass-title {
            font-size: 20px;
            color: #0c1220;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .pass-subtitle {
            font-size: 13px;
            color: #8a6d2f;
            margin: 0 0 15px 0;
        }
        .pass-footer {
            background: #f7f5ef;
            padding: 12px 20px;
            border-top: 1px solid #e3e0d8;
            font-size: 9px;
            color: #6b7688;
        }
    </style>
</head>
<body>
    @include('pdf.partials.head', ['kind' => 'Entry Pass'])

    <div class="pass-card">
        <div class="pass-header">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="color: #ffffff; font-weight: bold; font-size: 14px;">Math Nexus 2026</td>
                    <td style="text-align: right; color: #c8a863; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Entry Pass</td>
                </tr>
            </table>
        </div>
        <div class="pass-body">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 100px; vertical-align: top;">
                        @if ($photo)
                            <img src="{{ $photo }}" class="pass-photo" alt="Photo">
                        @else
                            <div class="pass-no-photo">
                                {{ mb_strtoupper(mb_substr($r->full_name_en, 0, 1)) }}
                            </div>
                        @endif
                    </td>
                    <td class="pass-details">
                        <div style="font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #8a6d2f; font-weight: bold; margin-bottom: 2px;">Reference</div>
                        <div style="font-size: 22px; font-weight: bold; color: #0c1220; letter-spacing: 1px; margin-bottom: 10px;">{{ $r->reference }}</div>
                        
                        <div class="pass-title">{{ $r->full_name_en }}</div>
                        @if ($r->full_name_bn)
                            <div style="font-size: 14px; color: #6b7688; margin-top: -3px; margin-bottom: 15px;">{{ $r->full_name_bn }}</div>
                        @endif

                        <table class="data" style="margin-top: 10px;">
                            <tr>
                                <td class="k" style="width: 30%;">Category</td>
                                <td class="v">{{ $r->category_label }}</td>
                                <td class="k" style="width: 30%;">Session</td>
                                <td class="v">{{ $r->session ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="k">T-shirt Size</td>
                                <td class="v">{{ $r->tshirt_size }}</td>
                                <td class="k">Accompanying Guests</td>
                                <td class="v">{{ $r->guest_total }}</td>
                            </tr>
                            <tr>
                                <td class="k">Blood Group</td>
                                <td class="v">{{ $r->blood_group ?: '—' }}</td>
                                <td class="k">Cultural Programme</td>
                                <td class="v">{{ $r->cultural_program ? 'Performing' : 'No' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            @if ($r->guests)
                <div style="margin-top: 20px; border-top: 1px solid #f0eee8; padding-top: 10px;">
                    <span style="font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #6b7688; font-weight: bold;">Accompanying Guests</span>
                    <div style="font-size: 11px; color: #10182a; font-weight: bold; margin-top: 3px;">
                        {{ collect($r->guests)->pluck('name')->filter()->implode(' &middot; ') }}
                    </div>
                </div>
            @endif
        </div>
        <div class="pass-footer">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td>
                        <strong>{{ \Carbon\Carbon::parse(config('rcmaa.registration.event_date'))->format('l, j F Y') }}</strong><br>
                        Rajshahi College Campus, Rajshahi
                    </td>
                    <td style="text-align: right; vertical-align: middle;">
                        Helpline: {{ config('rcmaa.contact.helpline') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="foot">
        {{ config('rcmaa.name') }} &middot; Generated {{ now()->format('j F Y, g:ia') }}
    </div>
</body>
</html>
