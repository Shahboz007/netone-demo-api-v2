<?php

namespace App\Exceptions;

use Exception;

class InvalidDataException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage() ?: "Kiritilgan ma'lumot noto'g'ri!"
        ], 422);
    }
}
