<?php

namespace App\Interfaces;

interface PaymentProcessorInterface
{
    public function processPayment(float $amount): void;
}
