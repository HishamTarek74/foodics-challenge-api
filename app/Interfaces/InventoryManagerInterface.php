<?php

namespace App\Interfaces;

use App\Models\Order;

interface InventoryManagerInterface
{
    public function updateInventory(Order $order): void;
}
