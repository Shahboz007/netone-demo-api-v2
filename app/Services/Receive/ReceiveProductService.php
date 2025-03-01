<?php

namespace App\Services\Receive;

use App\Models\ReceiveProduct;
use App\Services\Utils\DateFormatter;

class ReceiveProductService
{
    private string|null $startDate = null;
    private string|null $endDate = null;

    public function setDate(string $start, string $end): void
    {
        $this->startDate = DateFormatter::format($start, 'start');
        $this->endDate = DateFormatter::format($end, 'end');
    }

    public function findAll(): array
    {
        $query = ReceiveProduct::with(
            "user",
            "supplier",
            "status"
        );

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->orderByDesc('id')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();

        return [
            'data' => $data,
            'total_price' => $data->sum('total_price'),
            'total_count' => $data->count(),
        ];
    }

    public function create(array $data)
    {

    }

    public function findOne(int $id): array
    {
        $query = ReceiveProduct::with(
            "user",
            "supplier",
            "receiveProductDetails",
            "status"
        )->findOrFail($id);

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->firstOrFail();

        return [
            'data' => $data,
        ];
    }

}
