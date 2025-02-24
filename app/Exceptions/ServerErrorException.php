<?php

namespace App\Exceptions;

use Exception;

class ServerErrorException extends Exception
{
    public function render($request)
    {
        if (app()->environment('local') && config('app.debug')) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage() ?: "Serverda xatolik!",
                'trace'   => $this->getTrace(),
            ], 500);
        }

        return response()->json([
            'success' => false,
            'message' => $this->getMessage() ?: "Serverda xatolik yuz berdi!"
        ], 500);
    }
}
