<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PharmaciesSeeder extends Seeder
{
    /**
     * Seed pharmacies in Figueres.
     */
    public function run(): void
    {
        $now = now();

        $pharmacies = [
            ['name' => 'Farmàcia Soler', 'latitude' => 42.2655267, 'longitude' => 2.9631527],
            ['name' => 'Farmàcia Cullell Riera', 'latitude' => 42.2674554, 'longitude' => 2.9606029],
            ['name' => 'Farmàcia Olga Grau', 'latitude' => 42.2604143, 'longitude' => 2.9672931],
            ['name' => 'Farmàcia Manera', 'latitude' => 42.2671862, 'longitude' => 2.9617282],
            ['name' => 'Farmàcia Rodeja Roca, Adela', 'latitude' => 42.2664569, 'longitude' => 2.9685557],
            ['name' => 'Farmàcia Carme Rigall Carré', 'latitude' => 42.2725993, 'longitude' => 2.9557499],
            ['name' => 'Farmàcia Martínez Llonch', 'latitude' => 42.2644413, 'longitude' => 2.9616621],
            ['name' => 'Farmàcia Mallolas', 'latitude' => 42.2655140, 'longitude' => 2.9524376],
            ['name' => 'Farmàcia Torrent', 'latitude' => 42.2667292, 'longitude' => 2.9599356],
            ['name' => 'Farmàcia Domenech Gurt', 'latitude' => 42.2670706, 'longitude' => 2.9826612],
            ['name' => 'Farmàcia Bonmatí', 'latitude' => 42.2656407, 'longitude' => 2.9549869],
            ['name' => 'Farmàcia Mas Guday', 'latitude' => 42.266292, 'longitude' => 2.9621519],
            ['name' => 'Farmàcia Clara Thió i Pera', 'latitude' => 42.2622228, 'longitude' => 2.9652818],
            ['name' => 'Farmàcia Sandra Vidal Pont', 'latitude' => 42.2672959, 'longitude' => 2.9637079],
        ];

        foreach ($pharmacies as $pharmacy) {
            DB::table('pharmacies')->updateOrInsert(
                ['name' => $pharmacy['name']],
                [
                    'latitude' => $pharmacy['latitude'],
                    'longitude' => $pharmacy['longitude'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
