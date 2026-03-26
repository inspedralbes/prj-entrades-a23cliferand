<?php

namespace Database\Seeders;

use App\Models\Sessio;
use Illuminate\Database\Seeder;

class SessioSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = [
            ['pellicula_id' => "tt37969901", 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => now()->addDays(1)->setHour(16)->setMinute(0)],
            ['pellicula_id' => "tt0117883", 'sala_id' => 3, 'tarifa_id' => 2, 'data_hora' => now()->addDays(1)->setHour(19)->setMinute(30)],
            ['pellicula_id' => "tt0203009", 'sala_id' => 4, 'tarifa_id' => 3, 'data_hora' => now()->addDays(1)->setHour(22)->setMinute(0)],
            ['pellicula_id' => "tt0203009", 'sala_id' => 1, 'tarifa_id' => 1, 'data_hora' => now()->addDays(2)->setHour(17)->setMinute(0)],
            ['pellicula_id' => "tt1615160", 'sala_id' => 2, 'tarifa_id' => 1, 'data_hora' => now()->addDays(2)->setHour(20)->setMinute(0)],
            ['pellicula_id' => "tt0499549", 'sala_id' => 5, 'tarifa_id' => 4, 'data_hora' => now()->addDays(1)->setHour(18)->setMinute(0)],
            ['pellicula_id' => "tt0499549", 'sala_id' => 2, 'tarifa_id' => 1, 'data_hora' => now()->addDays(3)->setHour(16)->setMinute(30)],
            ['pellicula_id' => "tt1740490", 'sala_id' => 3, 'tarifa_id' => 2, 'data_hora' => now()->addDays(3)->setHour(19)->setMinute(0)],
            ['pellicula_id' => "tt1740490", 'sala_id' => 4, 'tarifa_id' => 3, 'data_hora' => now()->addDays(2)->setHour(21)->setMinute(0)],
        ];

        foreach ($sessions as $sessio) {
            Sessio::create($sessio);
        }
    }
}
