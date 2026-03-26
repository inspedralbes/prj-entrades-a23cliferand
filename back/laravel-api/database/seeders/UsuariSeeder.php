<?php

namespace Database\Seeders;

use App\Models\Usuari;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariSeeder extends Seeder
{
    public function run(): void
    {
        $usuaris = [
            [
                'nom' => 'Usuari de Prova',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'nom' => 'Joan García',
                'email' => 'joan@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'nom' => 'Maria López',
                'email' => 'maria@example.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($usuaris as $usuari) {
            Usuari::create($usuari);
        }
    }
}
