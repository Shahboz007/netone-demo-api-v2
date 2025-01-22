<?php

namespace Database\Seeders;

use App\Models\AmountSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmountSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kg -> Qop
        AmountSettings::create([
            "type_from_id" => 1, // kg
            "amount_from" => 40,
            "type_to_id" => 2, // qop
            "amount_to" => 1
        ]);

        // Qop -> kg
        AmountSettings::create([
            "type_from_id" => 2, // qop
            "amount_from" => 1,
            "type_to_id" => 1, // kg
            "amount_to" => 40
        ]);
    }
}
