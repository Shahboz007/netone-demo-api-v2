<?php

namespace App\Services\Payment;

use App\Exceptions\ServerErrorException;
use App\Models\Payment;
use App\Models\Status;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\select;

class PaymentSetMoneyService
{
    public static function getStatus()
    {
        return Status::where('code', 'paymentSetMoney')->firstOrFail();
    }

    public function findAll()
    {
        return Payment::where('status_id', self::getStatus()->id)->orderBy('created_at', 'desc')->get();
    }

    public function findOne(int $id)
    {
        return Payment::where('id', $id)->where('status_id', self::getStatus()->id)->firstOrFail();
    }

    /**
     * @throws ServerErrorException
     */
    public function create(array $data): string
    {
        $userWalletId = $data['user_wallet_id'];
        $amount = $data['amount'];
        $rateAmount = $data['rate_amount'];
        $comment = $data['comment'];

        // User Wallet
        $userWallet = UserWallet::with('user', 'wallet')->findOrFail($userWalletId);

        DB::beginTransaction();
        try {
            // New Payment
            $newPayment = new Payment([
                'user_id' => auth()->id(),
                'status_id' => self::getStatus()->id,
                'comment' => $comment,
            ]);
            $userWallet->payments()->save($newPayment);

            // Attach Wallet
            $sumPrice = $amount * $rateAmount;
            $newPayment->wallets()->attach($userWallet->wallet_id, [
                'amount' => $amount,
                'rate_amount' => $rateAmount,
                'sum_price' => $sumPrice,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update Wallet Balance
            $userWallet->increment('amount', $amount);

            // Finish
            DB::commit();

            $userName = $userWallet->user->name;
            $walletName = $userWallet->wallet->name;
            $formatNum = number_format($sumPrice, 2, '.', ',');

            return "$userName foydalanuvchining `$walletName` hisobiga $formatNum uzs muvaffaqiyatli o'tkazildi";
        } catch (\Exception $e) {
            DB::rollback();
            throw new ServerErrorException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
