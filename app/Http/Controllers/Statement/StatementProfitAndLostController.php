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

        $list[] = $allMonthSales->getYearlySalePrice(["title" => "Daromad"]);
        $list[] = $allMonthSales->getYearlyCancelOrder(["title" => "Qaytarilgan"]);
        $list[] = $allMonthSales->getYearlySalePrice(["title" => "Sof daromad", "strong" => true]);
        $list[] = $allMonthSales->getYearlyCostPrice(["title" => "Sotilgan mahsulot narxi", "strong" => true]);
        $list[] = $allMonthSales->getYearlyCostPrice(["title" => "Tannarxi"]);
        $list[] = $allMonthSales->getYearlyShippingRawMaterial(["title" => "Xom ashyo yetkazib berish"]);
        $list[] = $allMonthSales->getYearlyMarja(["title" => "Marja", "strong" => true]);
        $list[] = $allMonthSales->getYearlyMarjaByPercent(["title" => "Marja rentabellik", "is_color" => true]);

        return response()->json([
            'data' => $list,
            'current_year' => $year,
            'current_month' => now()->month,
        ]);
    }
}
