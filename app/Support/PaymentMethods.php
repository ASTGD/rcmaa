<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * The payment methods actually on offer right now.
 *
 * config('rcmaa.payment.methods') declares what the association COULD collect
 * through; this narrows it to what is ready today. bKash is ready when it has
 * a number; Bangla QR is ready when the association's QR image exists on the
 * public disk. The form, the validator and the FAQ all read this one filter,
 * so a method can never be accepted by the server while hidden from the page —
 * or the other way round.
 */
class PaymentMethods
{
    /** @return array<string, array<string, mixed>> */
    public static function available(): array
    {
        return collect(config('rcmaa.payment.methods'))
            ->filter(fn ($m) => isset($m['number'])
                || (isset($m['qr_image']) && Storage::disk('public')->exists($m['qr_image'])))
            ->all();
    }

    /** @return list<string> */
    public static function keys(): array
    {
        return array_keys(self::available());
    }
}
