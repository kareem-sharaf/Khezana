<?php

namespace App\Listeners;

use App\Events\OrderPickupCompleted;
use App\Services\NotificationService;

/**
 * NotifyPickupCompleted Listener
 *
 * يخطر العميل بإكمال عملية الاستلام
 */
class NotifyPickupCompleted
{
    public function __construct(
        protected NotificationService $notificationService,
    ) {}

    public function handle(OrderPickupCompleted $event): void
    {
        $order = $event->order;

        // إرسال شكر وإشعار بإكمال العملية
        $this->notificationService->sendPickupCompletedNotification($order);
    }
}
