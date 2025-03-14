<?php

namespace App\Services\Payment;

use App\Exceptions\ServerErrorException;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\RentalProperty;
use App\Models\RentalPropertyAction;
use App\Models\RentalPropertyCategory;
use App\Models\UserWallet;
use App\Services\Status\StatusService;
use App\Services\Utils\DateFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentRentalPropertiesService
{

    public function getIncomeStatus()
    {
        return StatusService::findByCode('paymentIncomeRentalProperty');
    }

    public function getExpenseStatus()
    {
        return StatusService::findByCode('paymentExpenseRentalProperty');
    }

    public function findAll(array $query): array
    {
        // Query
        $startDate = DateFormatter::format($query['startDate']);
        $endDate = DateFormatter::format($query['endDate'], 'end');
        $rentalPropertyId = $query['rental_property_id'] ?? null;
        $statusCode = $query['status_code'];

        $statusPayment = $statusCode == 'paymentIncomeRentalProperty' ? $this->getIncomeStatus() : $this->getExpenseStatus();

        $query = Payment::with([
            'user',
            'paymentable.rentalProperty',
            'paymentable.rentalPropertyCategory',
            'status'
        ])
            ->where('paymentable_type', 'App\Models\RentalPropertyAction')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status_id', $statusPayment->id);

        if ($rentalPropertyId) {
            $query->where('paymentable_id', $rentalPropertyId);
        }

        $data = $query
            ->orderBy('created_at', 'desc')
            ->get();

        // Totals
        $totals = $this->getTotals($rentalPropertyId, [$startDate, $endDate], $statusPayment->id);

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

        // Rental Property Category
        $rentalPropertyCategory = RentalPropertyCategory::findOrFail($reqRentalPropertyCategoryId);

        // Status Payment
        $statusPayment = $rentalPropertyCategory->is_income ? $this->getIncomeStatus() : $this->getExpenseStatus();

        // User Wallet
        $userWallet = UserWallet::with(['wallet.currency'])->findOrFail($userWalletId);

        DB::beginTransaction();
        try {
            // New Rental Property Action
            $newRentalAction = RentalPropertyAction::create([
                'rental_property_id' => $rentalProperty->id,
                'rental_property_category_id' => $rentalPropertyCategory->id,
                'is_income' => $rentalPropertyCategory->is_income,
            ]);
            // New Payment
            $newPayment = new Payment([
                'user_id' => Auth::id(),
                'comment' => $reqComment,
                'status_id' => $statusPayment->id,
                'total_amount' => 0
            ]);
            $newRentalAction->payments()->save($newPayment);

            // Attach Wallet
            $sumPrice = $reqAmount * $reqRateAmount;
            $newPayment->wallets()->attach($userWallet->wallet_id, [
                'amount' => $reqAmount,
                'rate_amount' => $reqRateAmount,
                'sum_price' => $sumPrice,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Set Payment Total Amount
            $newPayment->total_amount = $sumPrice;
            $newPayment->save();

            if($rentalPropertyCategory->is_income){
                // Update User Wallet
                $userWallet->increment('amount', $reqAmount);
            }else {
                // Update User Wallet
                $userWallet->decrement('amount', $reqAmount);
            }

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
            ->where('status_id', $this->getIncomeStatus()->id)
            ->findOrFail($id);

        return [
            'data' => $data,
        ];
    }

    private function getTotals(string|null $rentalPropertyId, array $date, $statusId): array
    {
        $query = DB::table('payments')
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select(
                DB::raw('SUM(payment_wallet.sum_price) as total_amount'),
                DB::raw('COUNT(payments.id) as total_count')
            )
            ->where('paymentable_type', 'App\Models\RentalPropertyAction')
            ->where('status_id', $statusId);

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
