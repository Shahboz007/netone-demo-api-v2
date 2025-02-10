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

        // Profit
        $list[0] = $allMonthSales->getYearlyProfit(["title" => "Daromad","is_diff" => true]);
        // Return Orders
        $list[1] = $allMonthSales->getYearlyReturnOrder(["title" => "Qaytarilgan"]);
        // Net Profit
        $list[2] = $allMonthSales->getYearlyNetProfit(["title" => "Sof daromad", "strong" => true]);
        $list[3] = $allMonthSales->getYearlyCostPrice(["title" => "Sotilgan mahsulot narxi", "strong" => true]);
        $list[4] = $allMonthSales->getYearlyCostPrice(["title" => "Tannarxi"]);
        $list[5] = $allMonthSales->getYearlyShippingRawMaterial(["title" => "Xom ashyo yetkazib berish"]);
        $list[6] = $allMonthSales->getYearlyMarja(["title" => "Marja", "strong" => true]);
        $list[7] = $allMonthSales->getYearlyMarjaByPercent(["title" => "Marja rentabellik", "is_color" => true]);

        return response()->json([
            'data' => $list,
            'current_year' => (int)$year,
            'current_month' => now()->month,
        ]);
    }
}
