<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AlumniPortalController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The alumni directory is for members only.
 *
 * The association's own FAQ says so — "শুধুমাত্র RCMAA-এর নিবন্ধিত সদস্যরাই
 * লগইন করার মাধ্যমে Alumni Database দেখতে পারবেন" — and the listing carries
 * mobile numbers, so a public page would have contradicted a promise alumni
 * were relying on.
 *
 * Two ways in: a registrant who has opened their emailed link, or a signed-in
 * committee member.
 */
class EnsureCanViewDirectory
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has(AlumniPortalController::SESSION_KEY)) {
            return $next($request);
        }

        if ($request->user()?->is_admin) {
            return $next($request);
        }

        return redirect()->route('portal.request')->with(
            'directory_gate',
            'The alumni directory is open to registered members. Enter the email address you registered with and we will send you a link.'
        );
    }
}
