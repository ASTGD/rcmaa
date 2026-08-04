<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('RCMAA_ADMIN_EMAIL', 'admin@rcmaa.bd')],
            [
                'name' => env('RCMAA_ADMIN_NAME', 'RCMAA Administrator'),
                // Overridden by RCMAA_ADMIN_PASSWORD in .env — change it before deploying.
                'password' => Hash::make(env('RCMAA_ADMIN_PASSWORD', 'password')),
                'is_admin' => true,
            ]
        );

        $this->call([ContentSeeder::class, CommitteeSeeder::class]);
    }
}
