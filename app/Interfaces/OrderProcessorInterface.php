<?php

namespace App\Interfaces;

use App\Models\Order;

interface OrderProcessorInterface
{
    public function process($dataOrder);
}
