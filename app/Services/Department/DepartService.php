<?php

namespace App\Services\Department;

use App\Models\Depart;

class DepartService
{
  public function findAll(): array
  {
    $data = Depart::with('user')->get();

    return [
      'data' => $data,
    ];
  }


  public function findOne(int $id): array
  {
    $data = Depart::with('user')->findOrFail($id);

    return [
      'data' => $data
    ];
  }


  public function create(array $data)
  {
    // Create
    $newDepart = Depart::create($data);

    return [
      'message' => "Yangi $newDepart->name bo'lim muvaffaqiyatli yaratildi",
      'data' => $newDepart
    ];
  }

  public function update(array $data, int $id): array
  {

    $depart = Depart::with('user')->findOrFail($id);

    $depart->update($data);

    return [
      'message' => "$depart->name yangilandi",
      'data' => $depart
    ];
  }

  public function delete(int $id): array
  {
    $depart = Depart::with('user')->findOrFail($id);

    return [
      'message' => "$depart->name o'chirildi",
      'data' => $depart
    ];
  }
}
