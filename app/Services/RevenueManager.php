<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class RevenueManager
{
    /**
     * Calculate total revenue for all orders.
     *
     * @return float
     */
    public static function calculateTotalRevenue(): float
    {
//        $totalRevenue = 0.0;
//
//        Order::query()->chunk(100, function ($orders) use (&$totalRevenue) {
//            $orders->each(function ($order) use (&$totalRevenue) {
//                $order->items()->each(function ($orderItem) use (&$totalRevenue) {
//                    $totalRevenue += $orderItem->quantity * $orderItem->price;
//                });
//            });
//        });
//
//        return $totalRevenue;

        $totalRevenue = DB::table('order_items')
            ->select(DB::raw('SUM(quantity * price) as total_revenue'))
            ->value('total_revenue');

        // or adding total in orders table

        return $totalRevenue ?? 0.0;
    }
}
