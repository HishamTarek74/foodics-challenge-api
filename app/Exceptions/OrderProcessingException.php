<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class OrderProcessingException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = "Order processing failed: " . $message;
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'error' => 'Order Processing Error',
            'message' => $this->getMessage(),
        ], 400);
    }
}
