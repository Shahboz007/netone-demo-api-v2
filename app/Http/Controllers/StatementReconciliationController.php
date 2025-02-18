<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\Statement\StatementReconciliationService;
use Illuminate\Http\Request;

class StatementReconciliationController extends Controller
{
    public function __construct(
        protected StatementReconciliationService $reconciliationService
    ) {}

    public function show(int $customerId)
    {
        if(!Customer::findOrFail($customerId)) abort(404);

        $data = $this->reconciliationService->getByCustomer($customerId);

        return $data;
    }
}
