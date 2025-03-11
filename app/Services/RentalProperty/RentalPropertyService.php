<?php

namespace App\Services\RentalProperty;

use App\Models\RentalProperty;
use Illuminate\Support\Facades\DB;

class RentalPropertyService
{
    public function findAll(): array
    {
        // Data
        $data = RentalProperty::all();

        // Totals
        $totals = $this->getTotals();

        return [
            'data' => $data,
            'totals' => $totals,
        ];
    }

    public function create(array $data): array
    {
        // Data
        $reqName = $data['name'];
        $reqPrice = $data['price'];
        $reqComment = $data['comment'] ?? null;

        // Create
        $newRentalProperty = RentalProperty::create([
            'name' => $reqName,
            'price' => $reqPrice,
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
        $rental = RentalProperty::findOrFail($id);

        $rental->name = $data['name'] ?? $rental->name;
        $rental->price = $data['price'] ?? $rental->price;
        $rental->comment = $data['comment'] ?? $rental->comment;

        $rental->save();

        return [
            'message' => "Tijorat obyekti yangilandi",
            'data' => $rental,
        ];
    }

    public function delete(int $id): array
    {
        RentalProperty::findOrFail($id)->delete();

        return [
            'message' => "Tijorat obyekti muvaffaqiyatli o'chirildi",
        ];
    }

    private function getTotals(): array
    {
        $result = RentalProperty::select([
            DB::raw('SUM(price) as total_price'),
            DB::raw('COUNT(id) as total_count')
        ])
            ->first();

        return [
            'total_price' => (float)$result->total_price,
            'total_count' => (int)$result->total_count,
        ];
    }
}
