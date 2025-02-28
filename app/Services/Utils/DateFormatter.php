<?php

namespace App\Services\Utils;

use Carbon\Carbon;

class DateFormatter
{
    static public function format($date)
    {
        return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
    }
}
