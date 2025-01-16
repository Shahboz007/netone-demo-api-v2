<?php

namespace Database\Seeders;

use App\Models\Role;
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
        $admin = User::create([
            "name" => "Admin",
            "login" => "admin",
            "phone" => "998123456789",
            "password" => Hash::make("secret"),
        ]);
        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $admin->roles()->attach($adminRole);

        $producer = User::create([
            "name" => "Producer",
            "login" => "producer",
            "phone" => "998123456787",
            "password" => Hash::make("secret"),
        ]);
        $producerRole = Role::where('name', 'producer')->firstOrFail();
        $producer->roles()->attach($producerRole);
        $ordererRole = Role::where('name', 'orderer')->firstOrFail();
        $producer->roles()->attach($ordererRole);
    }
}
