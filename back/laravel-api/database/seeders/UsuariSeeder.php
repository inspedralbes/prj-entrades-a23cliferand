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
                'nom' => 'Maria López',
                'email' => 'client@client.com',
                'password' => Hash::make('Jupiter1'),
                'rol' => 'client',
            ],
            [
                'nom' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('Jupiter1'),
                'rol' => 'admin',
            ],
        ];

        foreach ($usuaris as $usuari) {
            Usuari::create($usuari);
        }
    }
}
