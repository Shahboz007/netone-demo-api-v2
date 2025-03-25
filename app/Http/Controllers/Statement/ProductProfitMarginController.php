<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryParameterRequest;
use App\Services\Statement\ProductProfitMarginService;
use Illuminate\Http\Request;

class ProductProfitMarginController extends Controller
{
    public function __construct(
        protected ProductProfitMarginService $productProfitMarginService
    ) {}

    public function index(QueryParameterRequest $request) 
    {
        $result = $this->productProfitMarginService->findAll($request->validated());

        return [
            'data' => $result['data'],
            'totals' => $result['totals']
        ];
    }
}
