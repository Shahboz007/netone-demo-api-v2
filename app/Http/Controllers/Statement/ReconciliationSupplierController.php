<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Supplier;
use App\Services\Statement\ReconciliationCustomerService;
use App\Services\Statement\ReconciliationSupplierService;

class ReconciliationSupplierController extends Controller
{
    public function __construct(
        protected ReconciliationSupplierService $reconciliationService
    ) {}

    public function show(int $supplierId)
    {
        // if(!Supplier::findOrFail($supplierId)) abort(404);

        // $data = $this->reconciliationService->getBySupplier($supplierId);

        // return $data;
    }
}
