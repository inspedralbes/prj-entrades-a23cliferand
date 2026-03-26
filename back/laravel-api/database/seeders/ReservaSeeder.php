<?php

namespace Database\Seeders;

use App\Models\Reserva;
use Illuminate\Database\Seeder;

class ReservaSeeder extends Seeder
{
    public function run(): void
    {
        $reserves = [
            [
                'usuari_id' => 1,
                'sessio_id' => 1,
                'preu_total' => 19.80,
                'estat' => 'confirmada',
            ],
            [
                'usuari_id' => 1,
                'sessio_id' => 3,
                'preu_total' => 29.80,
                'estat' => 'pendent',
            ],
        ];

        foreach ($reserves as $reserva) {
            Reserva::create($reserva);
        }
    }
}
