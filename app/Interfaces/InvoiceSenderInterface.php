<?php

namespace App\Interfaces;

use App\Models\Order;

interface InvoiceSenderInterface
{
    public function send(Order $order): void;
}
