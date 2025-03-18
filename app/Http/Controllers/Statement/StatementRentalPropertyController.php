<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryParameterRequest;
use App\Services\Statement\StatementRentalPropertyService;
use Illuminate\Http\Request;

class StatementRentalPropertyController extends Controller
{

    public function __construct(
        protected StatementRentalPropertyService $statementRentalPropertyService
    )
    {

    }
    public function index(QueryParameterRequest $request)
    {
        $result = $this->statementRentalPropertyService->findAll($request->validated());

        return response()->json([
            'data' => $result['data']
        ]);
    }
}
