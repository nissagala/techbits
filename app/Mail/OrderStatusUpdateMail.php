<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $newStatus,
    ) {}

    public function build()
    {
        return $this->subject('TechBits — Your order ' . $this->order->order_number . ' is now ' . ucfirst($this->newStatus))
            ->view('emails.order-status');
    }
}
