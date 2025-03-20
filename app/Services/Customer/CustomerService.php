<?php

namespace App\Services\Customer;

use App\Events\Customer\CustomerCreatedEvent;
use App\Events\Customer\CustomerDeletedEvent;
use App\Events\Customer\CustomerTelegramAddedEvent;
use App\Events\Customer\CustomerTelegramRemoveEvent;
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

    // Event
    CustomerCreatedEvent::dispatch($newCustomer);

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

  public function update(int $id, array $data)
  {
    // Data
    $phone = $data['phone'] ?? null;
    $telegram = $data['telegram'] ?? null;

    // Customer
    $customer = Customer::findOrFail($id);
    

    // Check if Phone and Telegram already exist
    if ($phone) {
      $phoneExists = Customer::where('phone', $phone)->where('id', '<>', $customer->id)->exists();
      if ($phoneExists) abort(422, "Bu telefon raqam allaqachon mavjud!");
    }
    if ($telegram) {
      $telegramExists = Customer::where('telegram', $telegram)->where('id', '<>', $customer->id)->exists();
      if ($telegramExists) abort(422, "Bu telegram allaqachon mavjud!");
    }
    
    // Event
    if(!$customer->telegram && $telegram){
      $customer->telegram = $telegram;
      CustomerTelegramAddedEvent::dispatch($customer);
    }else if($customer->telegram && !$telegram){
      CustomerTelegramRemoveEvent::dispatch($customer);
    }

    // Update
    $customer->update($data);

    
    
    return [
      'message' => "Mijoz muvaffaqiyatli tahrirlandi!",
      'data' => $customer,
    ];
  }

  public function delete(int $id)
  {
    $customer = Customer::findOrFail($id);


    // Delete Customer
    $customer->delete();

    // Event
    CustomerDeletedEvent::dispatch($customer);

    return [
      'message' => "Mijoz muvaffaqiyatli o'chirildi!",
      'data' => $customer,
    ];
  }
}
