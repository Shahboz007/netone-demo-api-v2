<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Services\Production\CheckStockService;

class CheckStockController extends Controller
{
    public function __construct(
        protected CheckStockService $checkStockService
    ) {}

    public function index()
    {
        $stockWithRecipe = $this->checkStockService->getAllByRecipe(1);

        return $stockWithRecipe;
    }
}
