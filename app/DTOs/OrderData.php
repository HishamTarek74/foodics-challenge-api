<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

class OrderData
{
    public function __construct(
        public readonly int $userId,
        public readonly int $branchId,
        public readonly Collection $items,
        public readonly string $name,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            branchId: $data['branch_id'],
            items: collect($data['items']),
            name: $data['name']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
           // 'total' => $this->total,
            'user_id' => $this->userId,
            'branch_id' => $this->branchId,
            'items' => $this->items->toArray(),

        ];
    }

    public function items(): Collection
    {
        return $this->items;
    }
}
