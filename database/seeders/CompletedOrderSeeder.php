<?php

namespace Database\Seeders;

use App\Models\CompletedOrder;
use Database\Factories\CompletedOrderFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompletedOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompletedOrder::factory()->count(1000)->create();
    }
}
