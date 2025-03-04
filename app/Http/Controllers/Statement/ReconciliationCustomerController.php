<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\Statement\ReconciliationCustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReconciliationCustomerController extends Controller
{
    public function __construct(
        protected ReconciliationCustomerService $reconciliationService
    )
    {
    }

    public function show(Request $request, int $customerId): JsonResponse
    {
        $validated = $request->validate([
            'startDate' => 'required|date|date_format:d-m-Y|before_or_equal:endDate',
            'endDate' => 'required|date|date_format:d-m-Y|after_or_equal:startDate',
        ]);

        if (!Customer::findOrFail($customerId)) abort(404);

        $this->reconciliationService->setDateInterVal($validated['startDate'], $validated['endDate']);
        $data = $this->reconciliationService->getByCustomer($customerId);

        return response()->json($data);
    }
}
