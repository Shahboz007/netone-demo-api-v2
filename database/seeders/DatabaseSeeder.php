<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

            // Optional
            // ProductSeeder::class,
            // CustomerSeeder::class,
            // ProductStockSeeder::class,
            // ExpenseSeeder::class,
            // WalletSeeder::class,
            // OrderSeeder::class,
            // CompletedOrderSeeder::class,
        ]);
    }
}
