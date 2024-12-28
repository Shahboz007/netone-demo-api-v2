<?php

namespace Database\Seeders;

use App\Models\AmountType;
use App\Models\RawMaterial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RawMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amountQop = AmountType::where('name', 'qop')->firstOrFail();
        $amountTonna = AmountType::where('name', 'tonna')->firstOrFail();


        RawMaterial::create([
            "name" => 'Mahsulot 1',
            "amount_type_id" => $amountQop->id,
            "amount" => rand(1, 100)
        ]);

        RawMaterial::create([
            "name" => 'Mahsulot 2',
            "amount_type_id" => $amountQop->id,
            "amount" => rand(1, 100)
        ]);
        RawMaterial::create([
            "name" => 'Mahsulot 3',
            "amount_type_id" => $amountTonna->id,
            "amount" => rand(1, 100)
        ]);
    }
}
