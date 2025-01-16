<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // IMPORTANT change the order is forbidden
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'producer']);
        Role::create(['name' => 'orderer']);
    }
}
