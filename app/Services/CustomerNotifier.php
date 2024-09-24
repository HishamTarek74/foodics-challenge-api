<?php

namespace App\Services;

use App\Models\Order;
use App\Notifications\OrderPlaced;
use Illuminate\Support\Facades\Notification;
use App\Interfaces\CustomerNotifierInterface;

class CustomerNotifier implements CustomerNotifierInterface
{

    public function notify(Order $order): void
    {
        // TODO: Implement notify() method.
        $order->load('user');
        Notification::send($order->user, new OrderPlaced($order));

    }
}
