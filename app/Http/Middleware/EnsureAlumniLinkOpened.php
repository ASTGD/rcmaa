<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Gate for the registrant's own area — set only by opening a valid signed link. */
class EnsureAlumniLinkOpened
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('alumni_registration_id')) {
            return redirect()->route('portal.request')
                ->withErrors(['email' => 'That link has expired or was already used. Request a new one below.']);
        }

        return $next($request);
    }
}
