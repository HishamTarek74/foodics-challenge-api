<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\{
    OrderValidatorInterface,
    OrderCalculatorInterface,
    OrderProcessorInterface,
    PaymentProcessorInterface,
    CustomerNotifierInterface,
    InvoiceSenderInterface,
    OrderRepositoryInterface,
    InventoryManagerInterface
};
use App\Services\{
    OrderValidator,
    OrderCalculator,
    OrderProcessor,
    PaymentProcessor,
    CustomerNotifier,
    InvoiceSender,
    OrderRepository,
    InventoryManager
};

class OrderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(OrderValidatorInterface::class, OrderValidator::class);
        $this->app->bind(OrderCalculatorInterface::class, OrderCalculator::class);
        $this->app->bind(OrderProcessorInterface::class, OrderProcessor::class);
        $this->app->bind(PaymentProcessorInterface::class, PaymentProcessor::class);
        $this->app->bind(CustomerNotifierInterface::class, CustomerNotifier::class);
        $this->app->bind(InvoiceSenderInterface::class, InvoiceSender::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(InventoryManagerInterface::class, InventoryManager::class);
    }
}
