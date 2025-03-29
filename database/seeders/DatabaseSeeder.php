<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\TransPerm;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Important
            RoleSeeder::class,
            UserSeeder::class,
            AmountTypeSeeder::class,
            StatusSeeder::class,
            AmountSettingsSeeder::class,
            CurrencySeeder::class,
            ExchangeRateSeeder::class,
            TransPerm::class,

            // Optional
            // ProductSeeder::class,
            // CustomerSeeder::class,
            // ProductStockSeeder::class,
            // ExpenseSeeder::class,
            // WalletSeeder::class,
            // OrderSeeder::class,
            // CompletedOrderSeeder::class,
            PolkaSeeder::class
        ]);
    }
}
