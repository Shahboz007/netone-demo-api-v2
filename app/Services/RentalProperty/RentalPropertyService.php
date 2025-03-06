<?php

namespace App\Services\RentalProperty;

use App\Models\RentalProperty;
use Illuminate\Database\Eloquent\Collection;
use staabm\SideEffectsDetector\SideEffect;

class RentalPropertyService
{
    public function findAll(): array
    {
        $data = RentalProperty::all();
        return [
            'data' => $data,
            'total_count' => $data->count(),
        ];
    }

    public function create(array $data): array
    {
        // Data
        $reqName = $data['name'];
        $reqAmount = $data['amount'];
        $reqComment = $data['comment'] ?? null;

        // Create
        $newRentalProperty = RentalProperty::create([
            'name' => $reqName,
            'amount' => $reqAmount,
            'comment' => $reqComment,
        ]);

        return [
            'data' => $newRentalProperty,
            'message' => "Yangi tijorat obyekti",
        ];
    }

    public function findOne(int $id): array
    {
        $data = RentalProperty::findOrFail($id);

        return [
            'data' => $data,
        ];
    }

    public function update(array $data, int $id): array
    {
        $rental = $this->findOne($id);
        $updateRental = $rental->update($data);

        return [
            'message' => "Tijorat obyekti yangilandi",
            'data' => $updateRental,
        ];
    }

    public function delete(int $id): array
    {
        $result = $this->findOne($id)->delete();

        return [
            'message' => $result['message'],
            'data'
        ];
    }
}
