<?php

namespace App\Services\RentalProperty;

use App\Models\RentalPropertyCategory;
use Exception;

class RentalPropertyCategoryService
{
  public function findAll()
  {
    $data = RentalPropertyCategory::all();

    return [
      'data' => $data
    ];
  }

  public function create(array $data)
  {
    // Data 
    $reqName = $data['name'];
    $reqIsIncome = (bool) $data['is_income'] ?? false;

    // New Rental Property Category
    $newData = RentalPropertyCategory::create([
      'name' => $reqName,
      'is_income' => $reqIsIncome
    ]);

    return [
      'data' => $newData,
      'message' => "Muvaffaqiyatli qo'shildi"
    ];
  }

  public function findOne(int $id)
  {
    $data = RentalPropertyCategory::findOrFail($id);

    return [
      'data' => $data
    ];
  }

  public function update(array $reqData, int $id)
  {
    // Find Data
    $updateData = RentalPropertyCategory::findOrFail($id);

    $updateData->update($reqData);
    
    return [
      'data' => $updateData,
      'message' => "Muvaffaqiyatli yangilandi"
    ];
  }

  public function delete(int $id)
  {
    try {
      RentalPropertyCategory::destroy($id);

      return [
        'message' => "Muvaffaqiyatli o'chirildi"
      ];
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }
}
