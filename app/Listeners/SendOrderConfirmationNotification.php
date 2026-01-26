<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Services\NotificationService;
use App\Services\QrService;

/**
 * SendOrderConfirmationNotification Listener
 *
 * يرسل تأكيد الطلب والـ QR (إن وجد) للعميل
 */
class SendOrderConfirmationNotification
{
    public function __construct(
        protected NotificationService $notificationService,
        protected QrService $qrService,
    ) {}

    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        // إرسال تأكيد الطلب
        $this->notificationService->sendOrderConfirmation($order);

        // إذا كان الطلب للاستلام من المتجر: إرسال QR
        if ($order->channel === 'IN_STORE_PICKUP' && $order->qrCode) {
            $this->notificationService->sendWhatsAppQr($order);
        }
    }
}
