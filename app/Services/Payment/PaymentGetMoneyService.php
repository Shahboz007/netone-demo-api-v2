<?php

namespace App\Services\Payment;

use App\Exceptions\InvalidDataException;
use App\Exceptions\ServerErrorException;
use App\Models\GetMoney;
use App\Models\Payment;
use App\Models\Status;
use App\Models\UserWallet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PaymentGetMoneyService
{
    public static function getStatus()
    {
        return Status::where('code', 'paymentGetMoney')->firstOrFail();
    }

    public function findAll()
    {
        // ...
    }

    public function findOne($id)
    {
        return GetMoney::findOrFail($id);
    }

    public function create(array $data): string
    {
        // Data
        $getMoneyId = $data['get_money_id'];
        $userWalletId = $data['user_wallet_id'];
        $amount = $data['amount'];
        $rateAmount = $data['rate_amount'];
        $comment  = $data['comment'] ?? '';


        // Status
        $status = PaymentGetMoneyService::getStatus();

        // Find
        $getMoney = $this->findOne($getMoneyId);

        // User Wallet
        $userWallet = UserWallet::with(['user', 'wallet'])->findOrFail($userWalletId);
        $userName = $userWallet->user->name;
        $userWalletName = $userWallet->wallet->name;

        if ($userWallet->amount < $amount) {
            throw new InvalidDataException($userName . "ning `$userWalletName` hisobida mablag' yetarli emas!");
        }

        DB::beginTransaction();

        try {
            // New Payment
            $newPayment = new Payment([
                'user_id' => auth()->id(),
                'status_id' => $status->id,
                'comment' => $comment
            ]);
            $getMoney->payments()->save($newPayment);

            // Attach Wallet
            $sumPrice = $amount * $rateAmount;
            $newPayment->wallets()->attach($userWallet->wallet_id, [
                'amount' => $amount,
                'rate_amount' => $rateAmount,
                'sum_price' => $sumPrice,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Decrement From User Wallet
            $userWallet->decrement('amount', $amount);


            DB::commit();
            // Finish

            $formatVal = number_format($sumPrice, 2, '.', ',');

            return "$userName foydalanuvchining `$userWalletName` hisobidan -$formatVal uzs yechib olindi";
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ServerErrorException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
