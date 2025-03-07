<?php

namespace App\Services\RentalProperty;

use App\Exceptions\InvalidDataException;
use App\Models\Customer;
use App\Models\CustomerRentalProperty;
use App\Models\RentalProperty;
use Illuminate\Support\Facades\Auth;

class CustomerRentalPropertyService
{
    public function findAll(): array
    {
        $data = CustomerRentalProperty::with([
            'user',
            'rentalProperty',
            'customer',
        ])
            ->orderBy('updated_at', 'desc')
            ->get();

        return [
            'data' => $data,
        ];
    }

    public function create(array $data)
    {
        // Data
        $reqRentalPropertyId = $data['rental_property_id'];
        $reqCustomerId = $data['customer_id'];
        $reqPrice = $data['price'];
        $reqComment = $data['comment'] ?? null;

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
    public function findOne(int $id): array
    {
        $data = CustomerRentalProperty::with([
            'user',
            'rentalProperty',
            'customer',
        ])
            ->findOrFail($id);

        return [
            'data' => $data,
        ];
    }

    public function update(array $reqData, int $id)
    {
        // Request Data
        $reqRentalPropertyId = $reqData['rental_property_id'];
        $reqCustomerId = $reqData['customer_id'];
        $reqPrice = $reqData['price'];
        $reqComment = $reqData['comment'] ?? null;

        // Customer
        $customer = Customer::findOrFail($reqCustomerId);

        // Rental Property
        $rentalProperty = RentalProperty::findOrFail($reqRentalPropertyId);

        // Validation
        $exists = CustomerRentalProperty::where('rental_property_id', $rentalProperty->id)
            ->where('customer_id', $customer->id)
            ->where('id', '<>', $id)
            ->exists();
        if ($exists) {
            throw new InvalidDataException("$customer->first_name $customer->last_name mijoz uchun allaqchon $rentalProperty->name tijoray obyekti ochilgan");
        }

        $data = CustomerRentalProperty::findOrFail($id);

        // Update data
        $data->rental_property_id = $reqRentalPropertyId ?? $data->rental_property_id;
        $data->customer_id = $reqCustomerId ?? $data->customer_id;
        $data->price = $reqPrice ?? $data->price;
        $data->comment = $reqComment ?? $data->comment;
    }
}
