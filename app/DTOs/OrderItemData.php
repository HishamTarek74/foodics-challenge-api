<?php

namespace App\DTOs;

use App\Models\OrderItem;

class OrderItemData
{
    public function __construct(
        public int $productId,
        public float $price,
        public int $quantity
    ) {
    }

    public static function fromEloquentModel(OrderItem $orderItem): self
    {
        return new self($orderItem->product_id, $orderItem->price, $orderItem->quantity);
    }

}
