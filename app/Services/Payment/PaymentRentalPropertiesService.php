<?php

namespace App\Services\Payment;

use App\Exceptions\ServerErrorException;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\RentalProperty;
use App\Models\RentalPropertyAction;
use App\Models\UserWallet;
use App\Services\Status\StatusService;
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
            'paymentable.user',
            'paymentable.rentalProperty',
            'paymentable.customer',
            'status'
        ])
            ->where('paymentable_type', 'App\Models\RentalPropertyAction')
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
        $reqCustomerId = $data['customer_id'];
        $userWalletId = $data['user_wallet_id'];
        $reqAmount = $data['amount'];
        $reqRateAmount = $data['rate_amount'];
        $reqComment = $data['comment'] ?? null;

        // Customer
        $customer = Customer::findOrFail($reqCustomerId);

        // User Wallet
        $userWallet = UserWallet::with(['wallet.currency'])->findOrFail($userWalletId);

        // Payment Rental Status
        $status = StatusService::findByCode('paymentRentalProperty');

        DB::beginTransaction();
        try {
            // New Rental Property Action
            $newRentalProperty = RentalPropertyAction::create([
                'rental_property_id' => $reqRentalPropertyId,
                'total_price' => 0,
                'user_id' => auth()->id(),
                'customer_id' => $reqCustomerId,
                'user_wallet_id' => $userWallet->id,
            ]);

            // New Payment
            $newPayment = new Payment([
                'user_id' => auth()->id(),
                'comment' => $reqComment,
                'status_id' => $status->id,
            ]);
            $newRentalProperty->payments()->save($newPayment);

            // Attach Wallet
            $sumPrice = $reqAmount * $reqRateAmount;
            $newPayment->wallets()->attach($userWalletId, [
                'amount' => $reqAmount,
                'rate_amount' => $reqRateAmount,
                'sum_price' => $sumPrice,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Change Total Price Of Rental Property Action
            $newRentalProperty->total_price = $sumPrice;

            // Update User Wallet
            $userWallet->increment('amount', $reqAmount);

            DB::commit();

            $currencyCode = $userWallet->wallet->currency->code;
            $formatVal = number_format(100000, 2);
            return [
                'message' => "Tijorat obyekti uchun $customer->first_name $customer->last_name  mijozdan $formatVal $currencyCode o'tkazma muvaffaqiyatli qabul qilindi",
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw new ServerErrorException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function findOne(int $id): array
    {
        $data = Payment::with([
            'paymentable.user',
            'paymentable.rentalProperty',
            'paymentable.customer',
            'paymentable.userWallet.user',
            'paymentable.userWallet.wallet',
            'wallets',
            'status'
        ])
            ->where('paymentable_type', 'App\Models\RentalPropertyAction')
            ->where('status_id', $this->getStatus()->id)
            ->findOrFail($id);

        return [
            'data' => $data,
        ];
    }
}
