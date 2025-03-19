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

  public function findOne(int $id): array
  {
    $data = Customer::findOrFail($id);

    return [
      'data' => $data
    ];
  }
}
