<?php

namespace App\Http\Controllers;

use App\DTOs\OrderData;
use App\Exceptions\OrderProcessingException;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;

class OrderController extends Controller
{

    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
       // dd(OrderData::fromArray($request->validated())->toArray());
        try {
            $orderData = OrderData::fromArray($request->validated())->toArray();

            $this->orderService->placeOrder($orderData);

            return response()->json([
                'message' => 'Order placed successfully'
            ], 201);
        } catch (OrderProcessingException $e) {
            return response()->json([
                'error' => 'Order processing failed',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
