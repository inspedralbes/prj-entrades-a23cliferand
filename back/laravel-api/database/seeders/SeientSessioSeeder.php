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

        // Obtenir totes les sessions de la base de dades
        $sessions = \App\Models\Sessio::pluck('id');

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
