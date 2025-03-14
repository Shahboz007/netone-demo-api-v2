<?php

namespace App\Services\RentalProperty;

use App\Exceptions\InvalidDataException;
use App\Models\RentalPropertyCategory;
use Exception;

class RentalPropertyCategoryService
{
  public function findAll(array $params)
  {
    // Params
    $paramIsTree = (bool) $params["is_tree"] ?? false;
    $paramIsIncome = (bool) $params["is_income"] ?? false;

    $query = RentalPropertyCategory::query()->where('is_income', $paramIsIncome);

    if ($paramIsTree) $query->with('children')->whereNull('parent_id');
    

    $data = $query->get();

    return [
      'data' => $data
    ];
  }

  public function create(array $data)
  {
    // Data 
    $reqParentId = $data['parent_id'] ?? null;
    $reqName = $data['name'];
    $reqIsIncome = (bool) $data['is_income'] ?? false;

    // New Rental Property Category
    $newData = RentalPropertyCategory::create([
      'parent_id' => $reqParentId,
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
    $reqParentId = $reqData['parent_id'] ?? null;

    // Check Parent And Children
    if ($id === $reqParentId) {
      throw new InvalidDataException("Ota kategoriyani noto'g'ri tanlamoqdasiz");
    }

    $childIds = RentalPropertyCategory::where('parent_id', $id)
      ->orWhereIn('parent_id', function ($query) use ($id) {
        $query->select('id')
          ->from('rental_property_categories')
          ->where('parent_id', $id);
      })
      ->pluck('id')
      ->toArray();

    if (in_array($reqParentId, $childIds)) {
      throw new InvalidDataException("Ota kategoriyani noto'g'ri tanlamoqdasiz");
    }

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
