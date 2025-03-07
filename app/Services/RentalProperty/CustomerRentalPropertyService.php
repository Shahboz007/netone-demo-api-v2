<?php

namespace App\Services\RentalProperty;

use App\Exceptions\InvalidDataException;
use App\Models\Customer;
use App\Models\CustomerRentalProperty;
use App\Models\RentalProperty;
use Illuminate\Support\Facades\Auth;

class CustomerRentalPropertyService
{
    public function findAll() {}

    public function create(array $data)
    {
        // Data
        $reqRentalPropertyId = $data['rental_property_id'];
        $reqCustomerId = $data['customer_id'];
        $reqPrice = $data['price'];
        $reqComment = $data['comment'];

        // Customer
        $customer = Customer::findOrFail($reqCustomerId);

        // Rental Property
        $rentalProperty = RentalProperty::findOrFail($reqRentalPropertyId);

        $exists = CustomerRentalProperty::where('rental_property_id', $rentalProperty->id)
            ->where('customer_id', $customer->id)
            ->exists();
        if ($exists) {
            throw new InvalidDataException("$customer->first_name $customer->last_name mijoz uchun allaqchon $rentalProperty->name tijoray obyekti ochilgan");
        }

        // New Customer Rental Property
        $newData = CustomerRentalProperty::create([
            'rental_property_id' => $rentalProperty->id,
            'customer_id' => $customer->id,
            'user_id' => Auth::id(),
            'price' => $reqPrice,
            'comment' => $reqComment,
        ]);

        return [
            'message' => "$customer->first_name $customer->last_name mijoz uchun $rentalProperty->name tijorat obyekti ochildi",
            'data' => $newData
        ];
    }
    public function findOne($id) {}
}
