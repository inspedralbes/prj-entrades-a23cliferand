<?php

namespace Database\Seeders;

use App\Models\PreuTarifa;
use Illuminate\Database\Seeder;

class PreuTarifaSeeder extends Seeder
{
    public function run(): void
    {
        $preus = [
            ['tarifa_id' => 1, 'tipus_client_id' => 1, 'preu' => 9.90],
            ['tarifa_id' => 1, 'tipus_client_id' => 2, 'preu' => 5.50],
            ['tarifa_id' => 1, 'tipus_client_id' => 3, 'preu' => 6.50],
            ['tarifa_id' => 1, 'tipus_client_id' => 4, 'preu' => 7.50],
            ['tarifa_id' => 1, 'tipus_client_id' => 5, 'preu' => 7.50],
            ['tarifa_id' => 2, 'tipus_client_id' => 1, 'preu' => 11.90],
            ['tarifa_id' => 2, 'tipus_client_id' => 2, 'preu' => 7.50],
            ['tarifa_id' => 2, 'tipus_client_id' => 3, 'preu' => 8.50],
            ['tarifa_id' => 2, 'tipus_client_id' => 4, 'preu' => 9.50],
            ['tarifa_id' => 2, 'tipus_client_id' => 5, 'preu' => 9.50],
            ['tarifa_id' => 3, 'tipus_client_id' => 1, 'preu' => 14.90],
            ['tarifa_id' => 3, 'tipus_client_id' => 2, 'preu' => 9.50],
            ['tarifa_id' => 3, 'tipus_client_id' => 3, 'preu' => 10.50],
            ['tarifa_id' => 3, 'tipus_client_id' => 4, 'preu' => 11.50],
            ['tarifa_id' => 3, 'tipus_client_id' => 5, 'preu' => 11.50],
            ['tarifa_id' => 4, 'tipus_client_id' => 1, 'preu' => 18.90],
            ['tarifa_id' => 4, 'tipus_client_id' => 2, 'preu' => 12.50],
            ['tarifa_id' => 4, 'tipus_client_id' => 3, 'preu' => 13.50],
            ['tarifa_id' => 4, 'tipus_client_id' => 4, 'preu' => 14.50],
            ['tarifa_id' => 4, 'tipus_client_id' => 5, 'preu' => 14.50],
        ];

        foreach ($preus as $preu) {
            PreuTarifa::create($preu);
        }
    }
}
