<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Order
        Status::create(["name" => "Yangi", "code" => "orderNew"]);
        Status::create(["name" => "Jarayonda", "code" => "orderInProgress"]);
        Status::create(["name" => "Bekor qilindi", "code" => "orderCancel"]);
        Status::create(["name" => "Topshirildi", "code" => "orderCompleted"]);
    }
}
