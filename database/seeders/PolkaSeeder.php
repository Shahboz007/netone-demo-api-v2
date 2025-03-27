<?php

namespace Database\Seeders;

use App\Models\Polka;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PolkaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Polka::create(['name' => "Polka A"]);
        Polka::create(['name' => "Polka A - 01", "parent_id" => 1]);
        Polka::create(['name' => "Polka B"]);
        Polka::create(['name' => "Polka B - 01", "parent_id" => 3]);
        Polka::create(['name' => "Polka B - 02 - xs", "parent_id" => 4]);
        Polka::create(['name' => "Polka B - 02 - sm", "parent_id" => 4]);
    }
}
