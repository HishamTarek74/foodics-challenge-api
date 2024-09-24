<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order_success()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $firstProduct = Product::factory()->create();
        $secondProduct = Product::factory()->create();
        $branch = Branch::factory()->create();

        $response = $this->postJson('/api/orders', [
            'name' => 'Order 1',
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'items' => [
                ['product_id' => $firstProduct->id, 'quantity' => 2],
                ['product_id' => $secondProduct->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', ['name' => 'Order 1']);
    }

    public function test_rate_limiting_per_branch()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/orders', [
                'name' => 'Order ' . $i,
                'total' => 100,
                'user_id' => $user->id,
                'branch_id' => 1,
                'items' => [
                    ['product_id' => 1, 'quantity' => 2],
                    ['product_id' => 2, 'quantity' => 1],
                ],
            ]);
        }

        $response = $this->postJson('/api/orders', [
            'name' => 'Order 11',
            'total' => 100,
            'user_id' => $user->id,
            'branch_id' => 1,
            'items' => [
                ['product_id' => 1, 'quantity' => 2],
                ['product_id' => 2, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(429);
    }
}
