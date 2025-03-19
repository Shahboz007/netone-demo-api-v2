<?php

namespace App\Services\Customer;

use App\Models\Customer;

class CustomerService
{

  public function findAll(): array
  {
    $data = Customer::all();

    return [
      "data" => $data,
    ];
  }

  public function create(array $data)
  {
    // Data
    $telegram = $data["telegram"] ?? null;

    // New Customer
    $newCustomer = Customer::create($data);
    
    // Finish
    return [
      'message' => "Yangi mijoz muvaffaqiyatli qo'shildi!",
      'data' => $newCustomer,
    ];
  }

  public function findOne(int $id): array
  {
    $customer = Customer::findOrFail($id);

    return [
      'data' => $customer
    ];
  }
}
