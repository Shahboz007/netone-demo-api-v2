<?php

namespace App\Services\Payment;

use App\Exceptions\ServerErrorException;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\RentalProperty;
use App\Models\RentalPropertyAction;
use App\Models\UserWallet;
use App\Services\Status\StatusService;
use App\Services\Utils\DateFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentRentalPropertiesService
{
    public function getStatus()
    {
        return StatusService::findByCode('paymentRentalProperty');
    }

    public function findAll(array $query): array
    {
        // Query
        $startDate = DateFormatter::format($query['startDate']);
        $endDate = DateFormatter::format($query['endDate'], 'end');
        $rentalPropertyId = $query['rental_property_id'] ?? null;

        $query = Payment::with([
            'user',
            'paymentable.rentalProperty',
            'paymentable.rentalPropertyCategory',
            'status'
        ])
            ->where('paymentable_type', 'App\Models\RentalPropertyAction')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status_id', $this->getStatus()->id);
        
        if($rentalPropertyId){
            $query->where('paymentable_id', $rentalPropertyId);
        }

        $data = $query
            ->orderBy('created_at', 'desc')
            ->get();

        // Totals
        $totals = $this->getTotals($rentalPropertyId, [$startDate, $endDate]);

        return [
            'data' => $data,
            'totals' => $totals
        ];
    }

    /**
     * @throws ServerErrorException
     */
    public function create(array $data): array
    {
        // Data
        $reqRentalPropertyId = $data['rental_property_id'];
        $reqRentalPropertyCategoryId = $data['rental_property_category_id'];
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
            // New Rental Property Action
            $newRentalAction = RentalPropertyAction::create([
                'rental_property_id' => $rentalProperty->id,
                'rental_property_category_id' => $reqRentalPropertyCategoryId,
            ]);
            // New Payment
            $newPayment = new Payment([
                'user_id' => Auth::id(),
                'comment' => $reqComment,
                'status_id' => $status->id,
                'total_amount' => 0
            ]);
            $newRentalAction->payments()->save($newPayment);

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
            'paymentable.rentalProperty',
            'paymentable.rentalPropertyCategory',
            'status',
            'wallets',
        ])
            ->where('paymentable_type', 'App\Models\RentalPropertyAction')
            ->where('status_id', $this->getStatus()->id)
            ->findOrFail($id);

        return [
            'data' => $data,
        ];
    }

    private function getTotals(string|null $rentalPropertyId, array $date): array
    {
        $query = DB::table('payments')
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select(
                DB::raw('SUM(payment_wallet.sum_price) as total_amount'),
                DB::raw('COUNT(payments.id) as total_count')
            )
            ->where('paymentable_type', 'App\Models\RentalPropertyAction');

        if ($rentalPropertyId) {
            $query->where('payments.paymentable_id', $rentalPropertyId);
        }

        $query->whereBetween('payments.created_at', [$date[0], $date[1]])
            ->first();

        $data = $query->first();

        return [
            "total_amount" => (float)$data->total_amount,
            "total_count" => (int)$data->total_count,
        ];
    }
}
