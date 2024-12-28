<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function serverError()
    {
        abort(500, "Noma'lum xatolik. Qaytadan urinib ko'ring yoki biz bilan bog'laning!");
    }
}
