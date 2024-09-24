<?php

namespace App\Services;

use App\Exceptions\PaymentProcessingException;
use App\Interfaces\PaymentProcessorInterface;
use Illuminate\Support\Facades\RateLimiter;

class PaymentProcessor implements PaymentProcessorInterface
{

    public function processPayment(float $amount): void
    {
        // TODO: Implement processPayment() method.
        $executed = RateLimiter::attempt(
            'process-payment:' . auth()->id(),
            $perMinute = 5,
            function() use ($amount) {
                // Adding payment processing logic here
                // For example:
                // $result = $this->paymentGateway->charge($amount);
                // if (!$result->success) {
                //     throw new PaymentProcessingException($result->error);
                // }
            }
        );

        if (! $executed) {
            throw new PaymentProcessingException('Too many payment attempts. Please try again later.');
        }
    }
}
