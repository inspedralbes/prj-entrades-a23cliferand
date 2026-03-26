<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TipusClientSeeder::class,
            TarifaSeeder::class,
            SalaSeeder::class,
            PreuTarifaSeeder::class,
            UsuariSeeder::class,
            SessioSeeder::class,
            SeientSessioSeeder::class,
            ReservaSeeder::class,
            ReservaSeientSeeder::class,
        ]);
    }
}
