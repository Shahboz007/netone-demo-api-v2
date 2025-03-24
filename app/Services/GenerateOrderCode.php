<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;

class GenerateOrderCode
{
    public static function generate(int $customerId)
    {
        $lastOrder = Order::latest('id')->first();
        $lastOrderId = $lastOrder ? $lastOrder->ord_code : null;

        $date = Carbon::now();
        $yy = $date->format('y'); // Last two digits of the year
        $mm = $date->format('m'); // Month (zero-padded)
        $baseCode = "{$yy}{$mm}{$customerId}-";

        $nextNumber = 1;
        if ($lastOrderId) {
            $parts = explode('-', $lastOrderId);
            $lastNumber = (int) end($parts); // Get the last part as an integer
            $nextNumber = $lastNumber + 1;
        }

        $paddedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        return $baseCode. $paddedNumber;
    }
}
