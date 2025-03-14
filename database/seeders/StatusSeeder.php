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
        Status::create(["name" => "Tayyor", "code" => "orderCompleted"]);
        Status::create(["name" => "Topshirildi", "code" => "orderSubmitted"]);
        Status::create(["name" => "Qaytarildi", "code" => "orderReturned"]);

        // Production
        Status::create(['name' => 'Jarayonda', 'code' => 'productionProcess']);
        Status::create(['name' => 'Bekor qilindi', 'code' => 'productionCancel']);
        Status::create(['name' => "To'xtatildi", 'code' => 'productionStopped']);
        Status::create(['name' => "Tayyor", 'code' => 'productionCompleted']);

        // Payment
        Status::create(['name' => "Mijozdan o'tkazma", 'code' => 'paymentCustomer']);
        Status::create(['name' => "Taminotchiga o'tkazma", 'code' => 'paymentSupplier']);
        Status::create(['name' => "Xarajat", 'code' => 'paymentExpense']);
        Status::create(['name' => "Dividend", 'code' => 'paymentGetMoney']);
        Status::create(['name' => "Kassirga o'tkazma", 'code' => 'paymentSetMoney']);
        Status::create(['name' => "Tijorat obyektidan kirim", 'code' => 'paymentIncomeRentalProperty']);
        Status::create(['name' => "Tijorat obyektidan chiqim", 'code' => 'paymentExpenseRentalProperty']);

        // Receive Product
        Status::create(['name' => "To'lov qilinmagan", 'code' => 'receiveProductDebt']);
        Status::create(['name' => "To'lov qilingan", 'code' => 'receiveProductPayment']);
    }
}
