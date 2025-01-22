<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([
            ["name" => "Tayyor mahsulot 1", "cost_price" => 46000, "sale_price" => 49000, "updated_at" => now(), "created_at" => now()],
        ]);
        Product::insert([
            ["name" => "Tayyor mahsulot 2", "cost_price" => 46000, "sale_price" => 49000, "updated_at" => now(), "created_at" => now()],
            ["name" => "Tayyor mahsulot 3", "cost_price" => 46000, "sale_price" => 49000, "updated_at" => now(), "created_at" => now()],
            ["name" => "Tayyor mahsulot 4", "cost_price" => 46000, "sale_price" => 49000, "updated_at" => now(), "created_at" => now()],
            ["name" => "Tayyor mahsulot 5", "cost_price" => 46000, "sale_price" => 49000, "updated_at" => now(), "created_at" => now()],
        ]);
    }
}
