<?php

namespace App\Exceptions;

use Exception;

class PaymentProcessingException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'error' => 'Payment Processing Error',
            'message' => $this->getMessage(),
        ], 422);
    }
}
