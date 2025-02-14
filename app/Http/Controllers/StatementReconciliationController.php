<?php

namespace App\Http\Controllers;

use App\Services\Statement\StatementReconciliationService;
use Illuminate\Http\Request;

class StatementReconciliationController extends Controller
{
    public function __construct(
        protected StatementReconciliationService $reconciliationService
    ) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|integer|exists:customers,id'
        ]);

        $data = $this->reconciliationService->getAll($validated['customer_id'] || 0);

        return $data;
    }
}
