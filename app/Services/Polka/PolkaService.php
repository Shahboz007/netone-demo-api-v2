<?php

namespace App\Services\Polka;

use App\Models\Polka;

class PolkaService
{
  public function findAll(bool $isTree)
  {

    $query = Polka::query();

    if ($isTree) {
      $query->with('children')
        ->where('parent_id', null);
    }

    $data = $query->get();

    return [
      'data' => $data
    ];
  }
  public function findOne(int $id)
  {
    $data = Polka::findOrFail($id);

    return [
      'data' => $data
    ];
  }
  public function create(array $data)
  {
    // Create
    $polka = Polka::create($data);

    // Finish
    return [
      'message' => "Polka yaratildi",
      "data" => $polka
    ];
  }
  public function update(array $data, int $id)
  {
    // Polka
    $polka = Polka::findOrFail($id);

    $polka->update($data);

    return [
      'message' => "$polka->name yangilandi",
      "data" => $polka
    ];
  }
  public function delete(int $id)
  {
    // Polka
    $polka = Polka::findOrFail($id);

    $polka->delete();

    return [
      'message' => "$polka->name o'chirildi",
      "data" => $polka
    ];
  }
}
