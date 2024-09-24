<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order , $invoice)
    {
        $this->order = $order;

        $this->invoice = $invoice;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice for Order #' . $this->order->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'order' => $this->order,
                'invoice' => $this->invoice,
//                'totalAmount' => $this->order->total_amount,
//                'items' => $this->order->items,
//                'customerName' => $this->order->customer->name,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            // If you have a generated PDF invoice, you can attach it here
            // Attachment::fromPath('/path/to/invoice.pdf')->as('invoice.pdf')->withMime('application/pdf'),
        ];
    }
}
