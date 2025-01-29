<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('currencies')->insert([
            [
                'name' => 'Uzbekistan Som',
                'code' => 'UZS',
                'symbol' => 'so\'m',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'United States Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Russian Ruble',
                'code' => 'RUB',
                'symbol' => '₽',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
