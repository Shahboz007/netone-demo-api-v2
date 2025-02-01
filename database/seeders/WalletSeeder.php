<?php

namespace Database\Seeders;

use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wallet::create(['name' => 'Hisob 1', 'currency_id' => 1]);
        Wallet::create(['name' => 'Hisob 2', 'currency_id' => 2]);
    }
}
