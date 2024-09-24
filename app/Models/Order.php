<?php

namespace App\Models;

use App\DTOs\OrderData;
use App\DTOs\OrderItemData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'branch_id',
        'total',
    ];

    /**
     * Get the branch that owns the order.
     *
     * @return BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the order items for the order.
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the customer that owns the order.
     *
     * @return BelongsTo
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalAmount()
    {
        //we can use sum total_items price instead of adding total_amount
        return $this->total;
    }

    public function addOrderItems($items): void
    {
        $totalAmount = 0;
        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $orderItem = OrderItem::create([
                'order_id' => $this->id,
                'product_id' => $product->id,
                'price' => $product->price,
                'quantity' => $item['quantity'],
            ]);
            $this->items()->save($orderItem);

            $totalAmount += $product->price * $item['quantity'];
        }
        // Update the order total
        $this->total = $totalAmount;
        $this->save();
    }
}
