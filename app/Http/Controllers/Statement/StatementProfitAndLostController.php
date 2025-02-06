<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Services\Statement\StatementMonthlySales;

class StatementProfitAndLostController extends Controller
{
    public function index()
    {
        $year = 2024;

        $list[0] = StatementMonthlySales::getMonthlySales($year, "Sotishdan tushgan daromad");

        return $list;
    }
}
