<?php

use App\Http\Middleware\EnsureCanViewDirectory;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'directory' => EnsureCanViewDirectory::class,
        ]);

        // Two separate sign-ins live here: members under /my, and the committee
        // everywhere else. Sending a member to the committee's login — or the
        // reverse — is a dead end, so each is bounced back to its own.
        $middleware->redirectGuestsTo(
            fn (Request $request) => $request->is('my', 'my/*')
                ? route('member.login')
                : route('login')
        );

        $middleware->redirectUsersTo(
            fn (Request $request) => $request->is('my', 'my/*')
                ? route('member.dashboard')
                : route('admin.dashboard')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
