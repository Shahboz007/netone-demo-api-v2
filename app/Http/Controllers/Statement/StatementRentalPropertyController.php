<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Services\Statement\StatementRentalPropertyService;
use Illuminate\Http\Request;

class StatementRentalPropertyController extends Controller
{

    public function __construct(
        protected StatementRentalPropertyService $statementRentalPropertyService
    )
    {

    }
    public function index()
    {
        $result = $this->statementRentalPropertyService->findAll();

        return $result;
    }
}
