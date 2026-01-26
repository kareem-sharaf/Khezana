<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * OrderCancelled Event
 *
 * يتم إطلاقه عند إلغاء طلب
 */
class OrderCancelled
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public Order $order;
    public string $reason;

    public function __construct(Order $order, string $reason = '')
    {
        $this->order = $order;
        $this->reason = $reason;
    }

    public function broadcastOn()
    {
        return ['orders'];
    }

    public function broadcastAs()
    {
        return 'order.cancelled';
    }
}
