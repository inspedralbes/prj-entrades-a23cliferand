<?php

namespace Database\Seeders;

use App\Models\Sala;
use App\Models\SeientDisseny;
use Illuminate\Database\Seeder;

class SalaSeeder extends Seeder
{
    public function run(): void
    {
        $sales = [
            ['nom' => 'Sala 1', 'capacitat' => 96, 'files_max' => 8, 'columnes_max' => 12],
            ['nom' => 'Sala 2', 'capacitat' => 96, 'files_max' => 8, 'columnes_max' => 12],
            ['nom' => 'Sala 3 - 3D', 'capacitat' => 120, 'files_max' => 10, 'columnes_max' => 12],
            ['nom' => 'Sala 4 - IMAX', 'capacitat' => 200, 'files_max' => 15, 'columnes_max' => 14],
            ['nom' => 'Sala 5 - VIP', 'capacitat' => 40, 'files_max' => 5, 'columnes_max' => 8],
        ];

        foreach ($sales as $salaData) {
            $sala = Sala::create($salaData);
            $this->crearSeients($sala);
        }
    }

    private function crearSeients(Sala $sala): void
    {
        $filesMax = $sala->files_max;
        $columnesMax = $sala->columnes_max;
        $etiquetesFiles = range('A', 'Z');

        for ($fila = 1; $fila <= $filesMax; $fila++) {
            $etiquetaFila = $etiquetesFiles[$fila - 1];
            $numSeient = 0;

            for ($col = 1; $col <= $columnesMax; $col++) {
                $esSeient = $this->esSeient($fila, $col, $filesMax, $columnesMax, $sala->nom);

                if ($esSeient) {
                    $numSeient++;
                }

                SeientDisseny::create([
                    'sala_id' => $sala->id,
                    'fila' => $fila,
                    'columna' => $col,
                    'etiqueta_fila' => $etiquetaFila,
                    'num_seient' => $esSeient ? $numSeient : null,
                    'es_seient' => $esSeient,
                ]);
            }
        }
    }

    private function esSeient(int $fila, int $col, int $filesMax, int $columnesMax, string $nomSala): bool
    {
        if (str_contains($nomSala, 'VIP')) {
            return true;
        }

        if (str_contains($nomSala, 'IMAX')) {
            if ($fila >= 3 && $fila <= $filesMax - 2) {
                return true;
            }
            return $col >= 3 && $col <= $columnesMax - 2;
        }

        if (str_contains($nomSala, 'Sala 1')) {
            return true;
        }

        if ($col == 1 || $col == $columnesMax) {
            return false;
        }

        return true;
    }
}
