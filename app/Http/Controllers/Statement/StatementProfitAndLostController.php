<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Services\Statement\StatementYearlySales;

class StatementProfitAndLostController extends Controller
{
    public function index()
    {
        $year = 2024;

        $allMonthSales  = new StatementYearlySales($year);

        $list[] = $allMonthSales->getYearlySalePrice("Daromad");
        $list[] = $allMonthSales->getYearlyCostPrice("Tannarxi");
        $list[] = $allMonthSales->getYearlyMarja("Marja");
        $list[] = $allMonthSales->getYearlyMarjaByPercent("Marja rentabellik");

        return $list;
    }
}
