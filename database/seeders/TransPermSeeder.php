<?php

namespace Database\Seeders;

use App\Models\TransPerm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransPermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TransPerm::create(['name' => "Yukni bo'limlardan qabul qilish", "code" => "getProduct"]);
        TransPerm::create(['name' => "Yukni bo'limlarga yuborish", "code" => "setProduct"]);
    }
}
