<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            ['name' => 'Zona Norte', 'code' => 'NORTE'],
            ['name' => 'Zona Centro', 'code' => 'CENTRO'],
            ['name' => 'Zona Sur', 'code' => 'SUR'],
            ['name' => 'Zona Oriente', 'code' => 'ORIENTE'],
            ['name' => 'Zona Occidente', 'code' => 'OCCIDENTE'],
        ];

        foreach ($zones as $zone) {
            Zone::query()->updateOrCreate(
                ['code' => $zone['code']],
                [
                    'name' => $zone['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
