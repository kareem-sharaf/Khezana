<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use App\Services\NotificationService;

/**
 * NotifyOrderCancellation Listener
 *
 * يخطر العميل بإلغاء الطلب
 */
class NotifyOrderCancellation
{
    public function __construct(
        protected NotificationService $notificationService,
    ) {}

    public function handle(OrderCancelled $event): void
    {
        $order = $event->order;

        // إرسال إشعار بالإلغاء
        \Log::info('Order Cancelled Notification', [
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'reason' => $event->reason,
        ]);

        // يمكن إضافة إرسال بريد أو رسالة واتساب هنا
    }
}
