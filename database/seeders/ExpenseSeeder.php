<?php

namespace Database\Seeders;

use App\Models\Expense;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Expense::create(["name" => "Komunal", "amount" => 99000]);
        Expense::create(["name" => "Elektr", "amount" => 99000, "parent_id" => 1]);
        Expense::create(["name" => "Suv", "amount" => 99000, "parent_id" => 1]);
        Expense::create(["name" => "Gaz", "amount" => 99000, "parent_id" => 1]);
    }
}
