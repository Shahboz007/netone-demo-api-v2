<?php

namespace App\Http\Controllers;

use App\Services\Statement\StatementReconciliationService;
use Illuminate\Http\Request;

class StatementReconciliation extends Controller
{
    public function __construct(
        protected StatementReconciliationService $reconciliationService
    ) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id'
        ]);

        $data = $this->reconciliationService->getAll($validated['customer_id']);

        return $data;
    }
}
