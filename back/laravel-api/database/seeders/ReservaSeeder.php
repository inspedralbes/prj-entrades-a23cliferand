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
                'email' => 'user1@example.com',
                'sessio_id' => 1,
                'preu_total' => 19.80,
                'estat' => 'confirmada',
            ],
            [
                'email' => 'user2@example.com',
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
