<?php

namespace Database\Seeders;

use App\Models\Sessio;
use Illuminate\Database\Seeder;

class SessioSeeder extends Seeder
{
    private static $sessions = [
        // 4/4
        ['pellicula_id' => 'tt37969901', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-04 16:00:00'],
        ['pellicula_id' => 'tt0117883', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-04 18:30:00'],
        ['pellicula_id' => 'tt0203009', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-04 21:00:00'],
        ['pellicula_id' => 'tt1615160', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-04 16:00:00'],
        ['pellicula_id' => 'tt0499549', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-04 18:30:00'],
        ['pellicula_id' => 'tt1740490', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-04 21:00:00'],

        // 5/4
        ['pellicula_id' => 'tt37969901', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-05 16:00:00'],
        ['pellicula_id' => 'tt0117883', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-05 18:30:00'],
        ['pellicula_id' => 'tt0203009', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-05 21:00:00'],
        ['pellicula_id' => 'tt1615160', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-05 16:00:00'],
        ['pellicula_id' => 'tt0499549', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-05 18:30:00'],
        ['pellicula_id' => 'tt1740490', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-05 21:00:00'],

        // 6/4
        ['pellicula_id' => 'tt37969901', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-06 16:00:00'],
        ['pellicula_id' => 'tt0117883', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-06 18:30:00'],
        ['pellicula_id' => 'tt0203009', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-06 21:00:00'],
        ['pellicula_id' => 'tt1615160', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-06 16:00:00'],
        ['pellicula_id' => 'tt0499549', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-06 18:30:00'],
        ['pellicula_id' => 'tt1740490', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-06 21:00:00'],

        // 11/4
        ['pellicula_id' => 'tt37969901', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-11 16:00:00'],
        ['pellicula_id' => 'tt0117883', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-11 18:30:00'],
        ['pellicula_id' => 'tt0203009', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-11 21:00:00'],
        ['pellicula_id' => 'tt1615160', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-11 16:00:00'],
        ['pellicula_id' => 'tt0499549', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-11 18:30:00'],
        ['pellicula_id' => 'tt1740490', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-11 21:00:00'],

        // 12/4
        ['pellicula_id' => 'tt37969901', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-12 16:00:00'],
        ['pellicula_id' => 'tt0117883', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-12 18:30:00'],
        ['pellicula_id' => 'tt0203009', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-12 21:00:00'],
        ['pellicula_id' => 'tt1615160', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-12 16:00:00'],
        ['pellicula_id' => 'tt0499549', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-12 18:30:00'],
        ['pellicula_id' => 'tt1740490', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-12 21:00:00'],

        // 13/4
        ['pellicula_id' => 'tt37969901', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-13 16:00:00'],
        ['pellicula_id' => 'tt0117883', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-13 18:30:00'],
        ['pellicula_id' => 'tt0203009', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-13 21:00:00'],
        ['pellicula_id' => 'tt1615160', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-13 16:00:00'],
        ['pellicula_id' => 'tt0499549', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-13 18:30:00'],
        ['pellicula_id' => 'tt1740490', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-13 21:00:00'],

        // 18/4
        ['pellicula_id' => 'tt37969901', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-18 16:00:00'],
        ['pellicula_id' => 'tt0117883', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-18 18:30:00'],
        ['pellicula_id' => 'tt0203009', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-18 21:00:00'],
        ['pellicula_id' => 'tt1615160', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-18 16:00:00'],
        ['pellicula_id' => 'tt0499549', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-18 18:30:00'],
        ['pellicula_id' => 'tt1740490', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-18 21:00:00'],

        // 19/4
        ['pellicula_id' => 'tt37969901', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-19 16:00:00'],
        ['pellicula_id' => 'tt0117883', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-19 18:30:00'],
        ['pellicula_id' => 'tt0203009', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-19 21:00:00'],
        ['pellicula_id' => 'tt1615160', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-19 16:00:00'],
        ['pellicula_id' => 'tt0499549', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-19 18:30:00'],
        ['pellicula_id' => 'tt1740490', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-19 21:00:00'],

        // 20/4
        ['pellicula_id' => 'tt37969901', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-20 16:00:00'],
        ['pellicula_id' => 'tt0117883', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-20 18:30:00'],
        ['pellicula_id' => 'tt0203009', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-20 21:00:00'],
        ['pellicula_id' => 'tt1615160', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-20 16:00:00'],
        ['pellicula_id' => 'tt0499549', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-20 18:30:00'],
        ['pellicula_id' => 'tt1740490', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-20 21:00:00'],

        // 25/4
        ['pellicula_id' => 'tt37969901', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-25 16:00:00'],
        ['pellicula_id' => 'tt0117883', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-25 18:30:00'],
        ['pellicula_id' => 'tt0203009', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-25 21:00:00'],
        ['pellicula_id' => 'tt1615160', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-25 16:00:00'],
        ['pellicula_id' => 'tt0499549', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-25 18:30:00'],
        ['pellicula_id' => 'tt1740490', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-25 21:00:00'],

        // 26/4
        ['pellicula_id' => 'tt37969901', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-26 16:00:00'],
        ['pellicula_id' => 'tt0117883', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-26 18:30:00'],
        ['pellicula_id' => 'tt0203009', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-26 21:00:00'],
        ['pellicula_id' => 'tt1615160', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-26 16:00:00'],
        ['pellicula_id' => 'tt0499549', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-26 18:30:00'],
        ['pellicula_id' => 'tt1740490', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-26 21:00:00'],

        // 27/4
        ['pellicula_id' => 'tt37969901', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-27 16:00:00'],
        ['pellicula_id' => 'tt0117883', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-27 18:30:00'],
        ['pellicula_id' => 'tt0203009', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-27 21:00:00'],
        ['pellicula_id' => 'tt1615160', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-27 16:00:00'],
        ['pellicula_id' => 'tt0499549', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-27 18:30:00'],
        ['pellicula_id' => 'tt1740490', 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => '2026-04-27 21:00:00'],
    ];

    public function run(): void
    {
        foreach (self::$sessions as $sessio) {
            Sessio::create($sessio);
        }
    }
}
