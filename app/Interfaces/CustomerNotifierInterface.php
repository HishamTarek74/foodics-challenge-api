<?php

namespace App\Interfaces;

use App\Models\Order;

interface CustomerNotifierInterface
{
    public function notify(Order $order): void;
}
