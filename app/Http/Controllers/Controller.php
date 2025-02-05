<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function serverError($exception=null)
    {
        if (app()->environment('local')) {
            // Return detailed error message in development mode
            return response()->json([
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ], 500);
        }

        // Return a generic message in other environments
        abort(500, "Noma'lum xatolik. Qaytadan urinib ko'ring yoki biz bilan bog'laning!");
    }

    protected  function mainErrRes(string $message, $code = 422): JsonResponse
    {
        return response()->json([
           "message" => $message,
        ], $code);
    }
}
