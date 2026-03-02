<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@indicadoressj.local')],
            [
                'name' => env('ADMIN_NAME', 'Administrador SJ'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin12345!')),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        $zoneIds = Zone::query()->pluck('id')->all();
        $admin->zones()->sync($zoneIds);
    }
}
