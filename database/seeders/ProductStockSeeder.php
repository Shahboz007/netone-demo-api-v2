<?php

namespace Database\Seeders;

use App\Models\ProductStock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductStock::create([
            'product_id' => 1,
            "name" => "Sklad 1",
            "amount_type_id" => 1,
            "amount" => 673
        ]);
    }
}
