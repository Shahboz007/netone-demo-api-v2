<?php

namespace Database\Seeders;

use App\Models\AmountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AmountType::create(['name' => 'kg']);
        AmountType::create(['name' => 'qop']);
        AmountType::create(['name' => 'tonna']);
        AmountType::create(['name' => 'dona']);
    }
}
