<?php

namespace Database\Seeders;

use App\Models\Sala;
use Illuminate\Database\Seeder;

class SalaSeeder extends Seeder
{
    public function run(): void
    {
        $sales = [
            ['nom' => 'Sala 1', 'capacitat' => 100],
            ['nom' => 'Sala 2', 'capacitat' => 150],
            ['nom' => 'Sala 3 - 3D', 'capacitat' => 120],
            ['nom' => 'Sala 4 - IMAX', 'capacitat' => 200],
            ['nom' => 'Sala 5 - VIP', 'capacitat' => 50],
        ];

        foreach ($sales as $sala) {
            Sala::create($sala);
        }
    }
}
