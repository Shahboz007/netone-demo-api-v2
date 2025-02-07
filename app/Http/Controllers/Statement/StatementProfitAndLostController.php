<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Services\Statement\StatementYearlySales;
use Illuminate\Http\Request;

class StatementProfitAndLostController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'year' => [
                'required',
                'numeric',
                'min:2024',
                'max:' . now()->year, // Dynamically set the current year as max
            ],
        ]);

        $year = $validated['year'];

        $allMonthSales = new StatementYearlySales($year);

        $list[] = $allMonthSales->getYearlySalePrice("Daromad");
        $list[] = $allMonthSales->getYearlyCostPrice("Tannarxi");
        $list[] = $allMonthSales->getYearlyMarja("Marja");
        $list[] = $allMonthSales->getYearlyMarjaByPercent("Marja rentabellik");

        return $list;
    }
}
