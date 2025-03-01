<?php

namespace App\Service\Order;

use App\Models\Order;
use App\Services\Status\StatusService;
use App\Services\Utils\DateFormatter;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    private string|null $startDate = null;
    private string|null $endDate = null;

    public function setDate(string $start, string $end): void
    {
        $this->startDate = DateFormatter::format($start, 'start');
        $this->endDate = DateFormatter::format($end, 'end');
    }

    public function findAll($statusCode): Collection
    {
        $query = Order::with(
            'user',
            'customer',
            'status'
        );


        if ($statusCode) {
            $status = StatusService::findByCode($statusCode);

            // Submitted
            if ($statusCode === 'orderSubmitted') {
                $query->with('completedOrder');
            } else if ($statusCode === 'orderCancel') {
                $query->with('cancelOrder');
            }

            $query->where('status_id', $status->id);
        }

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return $query
            ->orderByDesc('created_at')
            ->whereBetween('updated_at', [$this->startDate, $this->endDate])
            ->get();
    }

    public function create()
    {
    }

    public function findOne()
    {
    }

    public function confirm()
    {
    }

    public function addProduct()
    {
    }

    public function completed()
    {
    }

    public function submit()
    {
    }
}
