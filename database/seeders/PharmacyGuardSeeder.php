<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PharmacyGuardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pharmacy_guards')->insert([
            [
                'date' => '2026-05-04',
                'pharmacy_id' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-05-05',
                'pharmacy_id' => 13,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-05-06',
                'pharmacy_id' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-05-07',
                'pharmacy_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-05-08',
                'pharmacy_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-05-09',
                'pharmacy_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-05-10',
                'pharmacy_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
