<?php

namespace App\Service\Order;

use App\Exceptions\ServerErrorException;
use App\Models\Order;
use App\Models\Product;
use App\Services\GenerateOrderCode;
use App\Services\Status\StatusService;
use App\Services\Utils\DateFormatter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * @throws ServerErrorException
     */
    public function create(array $data): array
    {
        // New Order status
        $newOrderStatus = StatusService::findByCode('orderNew');

        // Get Request Products
        $productsId = array_column($data['product_list'] ?? [], 'product_id');
        $products = Product::whereIn('id', $productsId)->get();

        $pluckedCostPrice = $products->pluck('cost_price', 'id');
        $pluckedSalePrice = $products->pluck('sale_price', 'id');

        // Generate Order Code
        $orderCode = GenerateOrderCode::generate($data['customer_id']);

        DB::beginTransaction();

        try {
            $newOrder = Order::create([
                "user_id" => auth()->id(),
                "customer_id" => $data['customer_id'],
                "status_id" => $newOrderStatus->id,
                'total_cost_price' => 0,
                'total_sale_price' => 0,
                'ord_code' => $orderCode
            ]);


            $totalCostPrice = 0;
            $totalSalePrice = 0;

            // Create Order Details
            $detailItemList = [];
            foreach ($data['product_list'] as $item) {
                $costPrice = $pluckedCostPrice[$item['product_id']];
                $salePrice = $pluckedSalePrice[$item['product_id']];
                $sumCostPrice = $costPrice * $item['amount'];
                $sumSalePrice = $salePrice * $item['amount'];

                $detailItemList[] = [
                    'product_id' => $item['product_id'],
                    'amount' => $item['amount'],
                    'amount_type_id' => $item['amount_type_id'],
                    'cost_price' => $costPrice,
                    'sale_price' => $salePrice,
                    'sum_cost_price' => $sumCostPrice,
                    'sum_sale_price' => $sumSalePrice,
                ];

                $totalCostPrice += $sumCostPrice;
                $totalSalePrice += $sumSalePrice;
            }

            $newOrder->orderDetails()->createMany($detailItemList);


            $newOrder->total_cost_price = $totalCostPrice;
            $newOrder->total_sale_price = $totalSalePrice;

            $newOrder->save();

            DB::commit();

            return [
                "message" => "Yangi buyurtma muvaffaqiyatli qo'shildi!",
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ServerErrorException($e->getMessage(), $e->getCode(), $e);
        }
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
