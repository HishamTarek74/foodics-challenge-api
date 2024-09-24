<?php

namespace App\Services;

use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderRepository implements OrderRepositoryInterface
{

    public function store($dataOrder)
    {
        // TODO: Implement store() method.
        $order = Order::create([
            'name' => $dataOrder['name'],
            'user_id' => $dataOrder['user_id'],
            'branch_id' => $dataOrder['branch_id']
        ]);

        $order->addOrderItems($dataOrder['items']);

        // Optionally cache the order
        //Cache::put("order:{$order->id}", $order, now()->addHours(24));

        return $order;
    }

    public function find(int $id): ?Order
    {
        return Cache::remember("order:{$id}", now()->addHours(24), function () use ($id) {
            return Order::find($id);
        });
    }
}
