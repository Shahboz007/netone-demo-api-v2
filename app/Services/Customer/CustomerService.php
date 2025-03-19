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

  public function findOne(array $id) {}
}
