<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         * Deliberately no Schema::defaultStringLength(191).
         *
         * It is a workaround for the 767-byte index limit on MySQL below 5.7 /
         * MariaDB below 10.2. Production is MariaDB 10.3, where every migration
         * here — including the unique index on registrations.email — applies
         * without it. Setting it would silently cap new string columns at 191
         * while their validation still accepts up to 255: a LinkedIn URL of 200
         * characters would pass the form and then fail the insert.
         */
    }
}
