<?php

namespace App\Services\Statement;

use App\Services\Utils\DateFormatter;
use Illuminate\Support\Facades\DB;

class StatementRentalPropertyService
{
  public function findAll(array $queryParam)
  {
    // Query Param
    $startDate = DateFormatter::format($queryParam['startDate']);
    $endDate = DateFormatter::format($queryParam['endDate'], 'end');

    $data = DB::table('rental_properties')
      ->join('rental_property_actions', 'rental_properties.id', '=', 'rental_property_actions.rental_property_id')
      ->join('payments', 'rental_property_actions.id', '=', 'payments.paymentable_id')
      ->join('statuses', 'payments.status_id', '=', 'statuses.id')
      ->select(
        'rental_properties.id',
        'rental_properties.name',
        DB::raw('COUNT(rental_property_actions.id) as action_count'),
        DB::raw('SUM(CASE WHEN statuses.code = "paymentIncomeRentalProperty" THEN payments.total_amount ELSE 0 END) as income_amount'),
        DB::raw('SUM(CASE WHEN statuses.code = "paymentIncomeRentalProperty" THEN 1 ELSE 0 END) as income_count'),
        DB::raw('SUM(CASE WHEN statuses.code = "paymentExpenseRentalProperty" THEN payments.total_amount ELSE 0 END) as expense_amount'),
        DB::raw('SUM(CASE WHEN statuses.code = "paymentExpenseRentalProperty" THEN 1 ELSE 0 END) as expense_count'),
        DB::raw('(
          GREATEST(
              (
                  SUM(
                      CASE 
                          WHEN statuses.code = "paymentIncomeRentalProperty" 
                          THEN payments.total_amount 
                          ELSE 0 
                      END
                  ) - 
                  SUM(
                      CASE 
                          WHEN statuses.code = "paymentExpenseRentalProperty" 
                          THEN payments.total_amount 
                          ELSE 0 
                      END
                  )
              ), 0
          )
      ) as diff_amount')
      
      )
      ->where('payments.paymentable_type', 'App\Models\RentalPropertyAction')
      ->whereBetween('payments.created_at', [$startDate, $endDate])
      ->groupBy('rental_properties.id', 'rental_properties.name')
      ->get();

    return ($data);
  }
}
