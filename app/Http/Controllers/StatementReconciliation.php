<?php

namespace App\Http\Controllers;

use App\Services\Statement\StatementReconciliationService;

class StatementReconciliation extends Controller
{
    public function __construct(
        protected StatementReconciliationService $reconciliationService
    ) {}

    public function index()
    {
        $data = $this->reconciliationService->getAll();

        return $data;
    }
}
