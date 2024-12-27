<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name" => "Admin",
            "login" => "admin",
            "phone" => "998123456789",
            "password" => Hash::make("secret"),
        ]);
        User::create([
            "name" => "Storekeeper",
            "login" => "storekeeper",
            "phone" => "998123456789",
            "password" => Hash::make("secret"),
        ]);
        User::create([
            "name" => "Producer",
            "login" => "producer",
            "phone" => "998123456789",
            "password" => Hash::make("secret"),
        ]);
        User::create([
            "name" => "Orderer",
            "login" => "orderer",
            "phone" => "998123456789",
            "password" => Hash::make("secret"),
        ]);
    }
}
