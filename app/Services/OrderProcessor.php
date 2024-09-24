<?php

namespace App\Services;

use App\Interfaces\InventoryManagerInterface;
use App\Interfaces\OrderProcessorInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderProcessor implements OrderProcessorInterface
{
    private $orderRepository;
    private $inventoryManager;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        InventoryManagerInterface $inventoryManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->inventoryManager = $inventoryManager;
    }

    public function process($dataOrder)
    {
        // TODO: Implement process() method.
        $order = null;

        DB::transaction(function () use ($dataOrder, &$order) {
            $order = $this->orderRepository->store($dataOrder);
            $this->inventoryManager->updateInventory($order);
        });

        return $order;
    }
}
