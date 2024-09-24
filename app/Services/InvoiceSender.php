<?php

namespace App\Services;

use App\Interfaces\InvoiceSenderInterface;
use App\Jobs\SendInvoice;
use App\Models\Order;

class InvoiceSender implements InvoiceSenderInterface
{

    public function send(Order $order): void
    {
        // TODO: Implement send() method.
        SendInvoice::dispatch($order)->onQueue('invoices');

    }
}
