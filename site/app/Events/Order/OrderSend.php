<?php

namespace App\Events\Order;

use App\Entities\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderSend
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public function __construct(private Order $order) {}
    
    public function broadcastOn(): Channel | array
    {
        return new PrivateChannel('order');
    }
}
