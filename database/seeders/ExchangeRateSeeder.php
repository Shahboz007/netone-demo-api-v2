<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExchangeRate::insert([
            [
                'from_currency_id' => 1,
                'to_currency_id' => 2,
                'rate' => '0.000077 ',
            ],
            [
                'from_currency_id' => 2,
                'to_currency_id' => 1,
                'rate' => '12953.45',
            ],
        ]);
    }
}
