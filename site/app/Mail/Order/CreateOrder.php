<?php

namespace App\Mail\Order;

use App\Entities\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreateOrder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function build(): self
    {
        return $this->subject('Оформление заказа в ' . env('APP_NAME'))
            ->view('emails.order.create');
    }
}
