<?php

namespace App\Services;

use App\Interfaces\InventoryManagerInterface;
use App\Models\Order;

class InventoryManager implements InventoryManagerInterface
{

    public function updateInventory(Order $order): void
    {
        // TODO: Implement updateInventory() method.
    }
}
