<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * The alumni directory is for members only.
 *
 * The association's own FAQ says so — "শুধুমাত্র RCMAA-এর নিবন্ধিত সদস্যরাই
 * লগইন করার মাধ্যমে Alumni Database দেখতে পারবেন" — and the listing carries
 * mobile numbers, so a public page would have contradicted a promise alumni
 * were relying on.
 *
 * A guest is no longer bounced to the login page. Their specification asks that
 * the public see the total number of registered members, so the controller
 * renders a locked view instead; this only records which of the two they get.
 */
class EnsureCanViewDirectory
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set(
            'directory_unlocked',
            Auth::guard('alumni')->check() || (bool) $request->user()?->is_admin
        );

        return $next($request);
    }
}
