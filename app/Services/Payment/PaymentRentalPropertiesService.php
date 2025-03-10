<?php

namespace App\Services\Payment;

use App\Exceptions\ServerErrorException;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\RentalProperty;
use App\Models\RentalPropertyAction;
use App\Models\UserWallet;
use App\Services\Status\StatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentRentalPropertiesService
{
    public function getStatus()
    {
        return StatusService::findByCode('paymentRentalProperty');
    }

    public function findAll(): array
    {
        $data = Payment::with([
            'user',
            'paymentable',
            'status'
        ])
            ->where('paymentable_type', 'App\Models\RentalProperty')
            ->where('status_id', $this->getStatus()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'data' => $data,
        ];
    }

    /**
     * @throws ServerErrorException
     */
    public function create(array $data): array
    {
        // Data
        $reqRentalPropertyId = $data['rental_property_id'];
        $userWalletId = $data['user_wallet_id'];
        $reqAmount = $data['amount'];
        $reqRateAmount = $data['rate_amount'];
        $reqComment = $data['comment'] ?? null;

        // Rental Property
        $rentalProperty = RentalProperty::findOrFail($reqRentalPropertyId);

        // User Wallet
        $userWallet = UserWallet::with(['wallet.currency'])->findOrFail($userWalletId);

        // Payment Rental Status
        $status = StatusService::findByCode('paymentRentalProperty');

        DB::beginTransaction();
        try {

            // New Payment
            $newPayment = new Payment([
                'user_id' => Auth::id(),
                'comment' => $reqComment,
                'status_id' => $status->id,
                'total_amount' => 0
            ]);
            $rentalProperty->payments()->save($newPayment);

            // Attach Wallet
            $sumPrice = $reqAmount * $reqRateAmount;
            $newPayment->wallets()->attach($userWalletId, [
                'amount' => $reqAmount,
                'rate_amount' => $reqRateAmount,
                'sum_price' => $sumPrice,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Set Payment Total Amount
            $newPayment->total_amount = $sumPrice;
            $newPayment->save();

            // Update User Wallet
            $userWallet->increment('amount', $reqAmount);

            DB::commit();

            $currencyCode = $userWallet->wallet->currency->code;
            $formatVal = number_format($reqAmount, 2);
            return [
                'message' => "$rentalProperty->name tijorat obyektidan  $formatVal $currencyCode o'tkazma muvaffaqiyatli qabul qilindi",
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ServerErrorException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function findOne(int $id): array
    {
        $data = Payment::with([
            'user',
            'paymentable',
            'status',
            'wallets',
        ])
            ->where('paymentable_type', 'App\Models\RentalProperty')
            ->where('status_id', $this->getStatus()->id)
            ->findOrFail($id);

        return [
            'data' => $data,
        ];
    }
}
