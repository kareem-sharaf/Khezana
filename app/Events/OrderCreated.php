<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * OrderCreated Event
 *
 * يتم إطلاقه عند إنشاء طلب جديد
 */
class OrderCreated
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
        return 'order.created';
    }
}
