<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class SendInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        $invoice = $this->generateInvoice();
        Mail::to($this->order->user->email)->send(new InvoiceMail($this->order ,$invoice));
    }

    private function generateInvoice()
    {
        // Logic to generate invoice goes here. This could involve creating a PDF, for example
        return 'Invoice content';
    }
}
