<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Supplier;
use App\Services\Statement\ReconciliationCustomerService;
use App\Services\Statement\ReconciliationSupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReconciliationSupplierController extends Controller
{
    public function __construct(
        protected ReconciliationSupplierService $reconciliationService
    ) {}

    public function show(Request $request, int $supplierId): JsonResponse
    {
        $validated = $request->validate([
            'startDate' => 'required|date|date_format:d-m-Y|before_or_equal:endDate',
            'endDate' => 'required|date|date_format:d-m-Y|after_or_equal:startDate',
        ]);

        if (!Customer::findOrFail($supplierId)) abort(404);

        $this->reconciliationService->setDateInterVal($validated['startDate'], $validated['endDate']);
        $data = $this->reconciliationService->getBySupplier($supplierId);

        return response()->json($data);
    }
}
