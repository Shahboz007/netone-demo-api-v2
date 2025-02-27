<?php

namespace App\Services\Utils;

use Carbon\Carbon;

class DateFormater
{
    static public function format($date): ?Carbon
    {
        return Carbon::createFromFormat('d-m-Y', $date);
    }
}
