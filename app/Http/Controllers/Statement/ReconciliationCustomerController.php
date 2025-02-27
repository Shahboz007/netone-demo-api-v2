<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\Statement\ReconciliationCustomerService;
use Illuminate\Http\JsonResponse;

class ReconciliationCustomerController extends Controller
{
    public function __construct(
        protected ReconciliationCustomerService $reconciliationService
    ) {}
    public function show(int $customerId): JsonResponse
    {
        if (!Customer::findOrFail($customerId)) abort(404);

        $data = $this->reconciliationService->getByCustomer($customerId);

        return response()->json($data);
    }
}
