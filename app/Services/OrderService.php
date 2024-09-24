<?php

namespace App\Services;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;
use App\Interfaces\{
    OrderValidatorInterface,
    OrderCalculatorInterface,
    OrderProcessorInterface,
    PaymentProcessorInterface,
    CustomerNotifierInterface,
    InvoiceSenderInterface
};

class OrderService
{

    /**
     * The order validator for validation order.
     *
     * @var
     */
    private $orderValidator;

    /**
     * The order calculator for calc details of order.
     *
     * @var
     */
    private $orderCalculator;

    /**
     * The order processor of order.
     *
     * @var
     */
    private $orderProcessor;

    /**
     * The payment processor for processing payment of order.
     *
     * @var
     */
    private $paymentProcessor;

    /**
     * The customer notifier for notifying customer.
     *
     * @var
     */
    private $customerNotifier;

    /**
     * The invoice sender  for sending order invoice.
     *
     * @var
     */
    private $invoiceSender;

    /**
     * The OrderService constructor.
     *
     * @param OrderValidatorInterface $orderValidator
     * @param OrderCalculatorInterface $orderCalculator
     * @param OrderProcessorInterface $orderProcessor
     * @param PaymentProcessorInterface $paymentProcessor
     * @param CustomerNotifierInterface $customerNotifier
     * @param InvoiceSenderInterface $invoiceSender
     */
    public function __construct(
        OrderValidatorInterface   $orderValidator,
        OrderCalculatorInterface  $orderCalculator,
        OrderProcessorInterface   $orderProcessor,
        PaymentProcessorInterface $paymentProcessor,
        CustomerNotifierInterface $customerNotifier,
        InvoiceSenderInterface    $invoiceSender
    )
    {
        $this->orderValidator = $orderValidator;
        $this->orderCalculator = $orderCalculator;
        $this->orderProcessor = $orderProcessor;
        $this->paymentProcessor = $paymentProcessor;
        $this->customerNotifier = $customerNotifier;
        $this->invoiceSender = $invoiceSender;
    }

    /**
     * Validates the order, calculates order details, processes the order and payment,
     * notifies the customer, and sends an invoice.
     *
     * @param $dataOrder
     *
     * @return void
     */
    public function placeOrder($dataOrder): void
    {
        try {
            app(Pipeline::class)
                ->send($dataOrder)
                ->through([
                    function ($dataOrder, $next) {
                        $this->orderValidator->validate($dataOrder);
                        return $next($dataOrder);
                    },
                    function ($dataOrder, $next) {
                        $this->orderCalculator->calculate($dataOrder);
                        return $next($dataOrder);
                    },
                    function ($dataOrder, $next) {
                        $order = $this->orderProcessor->process($dataOrder);
                        return $next($order);
                    },
                    function ($order, $next) {
                        $this->paymentProcessor->processPayment($order->getTotalAmount());
                        return $next($order);
                    },
                    function ($order, $next) {
                        $this->customerNotifier->notify($order);
                        return $next($order);
                    },
                    function ($order, $next) {
                        $this->invoiceSender->send($order);
                        return $next($order);
                    }
                ])
                ->then(function ($order) {
                    Log::info('Order placed successfully', ['order_id' => $order->id]);
                });
        } catch (\Exception $e) {
            Log::error('Order processing failed', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Failed to process order: ' . $e->getMessage());
        }
    }
}
