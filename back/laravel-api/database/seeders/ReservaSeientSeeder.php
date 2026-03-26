<?php

namespace Database\Seeders;

use App\Models\ReservaSeient;
use Illuminate\Database\Seeder;

class ReservaSeientSeeder extends Seeder
{
    public function run(): void
    {
        $reservaSeients = [
            [
                'reserva_id' => 1,
                'seient_sessio_id' => 1,
                'tipus_client_id' => 1,
                'preu_aplicat' => 9.90,
            ],
            [
                'reserva_id' => 1,
                'seient_sessio_id' => 2,
                'tipus_client_id' => 1,
                'preu_aplicat' => 9.90,
            ],
            [
                'reserva_id' => 2,
                'seient_sessio_id' => 25,
                'tipus_client_id' => 1,
                'preu_aplicat' => 14.90,
            ],
            [
                'reserva_id' => 2,
                'seient_sessio_id' => 26,
                'tipus_client_id' => 2,
                'preu_aplicat' => 9.50,
            ],
        ];

        foreach ($reservaSeients as $reservaSeient) {
            ReservaSeient::create($reservaSeient);
        }
    }
}
