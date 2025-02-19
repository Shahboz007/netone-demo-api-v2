<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Services\Statement\BalanceService;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function __construct(
        protected BalanceService $balanceService
    ) {}

    public function index()
    {
        $data = $this->balanceService->getBalance();

        return response()->json([
            "data" => $data,
        ]);
    }
}
