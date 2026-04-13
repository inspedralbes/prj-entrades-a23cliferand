<?php

namespace Database\Seeders;

use App\Models\TipusClient;
use Illuminate\Database\Seeder;

class TipusClientSeeder extends Seeder
{
    public function run(): void
    {
        $tipusClients = [
            ['nom' => 'Adult'],
            ['nom' => 'Infantil'],
            ['nom' => 'Jubilat'],
            ['nom' => 'Estudiant'],
            ['nom' => 'Familia Numerosa'],
        ];

        foreach ($tipusClients as $tipusClient) {
            TipusClient::create($tipusClient);
        }
    }
}
