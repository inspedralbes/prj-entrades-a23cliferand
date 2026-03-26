<?php

namespace Database\Seeders;

use App\Models\SeientSessio;
use Illuminate\Database\Seeder;

class SeientSessioSeeder extends Seeder
{
    public function run(): void
    {
        $files = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $maxSeientsPerFila = 12;

        $sessions = [1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($sessions as $sessioId) {
            foreach ($files as $fila) {
                for ($numero = 1; $numero <= $maxSeientsPerFila; $numero++) {
                    SeientSessio::create([
                        'sessio_id' => $sessioId,
                        'fila' => $fila,
                        'numero' => $numero,
                        'estat' => 'lliure',
                        'reservat_at' => null,
                    ]);
                }
            }
        }
    }
}
