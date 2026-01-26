<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * OrderPickupCompleted Event
 *
 * يتم إطلاقه عند إكمال عملية استلام الطلب من المتجر
 */
class OrderPickupCompleted
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return ['orders'];
    }

    public function broadcastAs()
    {
        return 'order.pickup-completed';
    }
}
