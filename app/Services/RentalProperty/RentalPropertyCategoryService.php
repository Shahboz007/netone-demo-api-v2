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
    $paramIsTree = $params["is_tree"] ?? false;

    $query = RentalPropertyCategory::query();

    if ($paramIsTree) {
      $query->with('children')
        ->whereNull('parent_id');
    }

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

    // Find Data
    $updateData = RentalPropertyCategory::findOrFail($id);

    // Check Parent And Children
    if ($reqParentId) {
      $pluckChildren = $updateData->children->pluck('name', 'id');

      if ($updateData->id === $reqParentId || !empty($pluckChildren[$reqParentId])) {
        throw new InvalidDataException("Ota kategoriya noto'g'ri tanlanmoqda!");
      }
    }

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
