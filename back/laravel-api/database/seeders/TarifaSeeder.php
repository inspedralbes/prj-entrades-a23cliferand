<?php

namespace Database\Seeders;

use App\Models\Tarifa;
use Illuminate\Database\Seeder;

class TarifaSeeder extends Seeder
{
    public function run(): void
    {
        $tarifes = [
            ['nom' => 'General'],
            ['nom' => '3D'],
            ['nom' => 'IMAX'],
            ['nom' => 'VIP'],
        ];

        foreach ($tarifes as $tarifa) {
            Tarifa::create($tarifa);
        }
    }
}
