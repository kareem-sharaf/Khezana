<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderQrCode;

/**
 * NotificationService
 *
 * خدمة الإشعارات - إرسال رسائل للعملاء عبر واتساب والبريد والتطبيق
 */
class NotificationService
{
    /**
     * إرسال تأكيد الطلب
     *
     * @param Order $order
     */
    public function sendOrderConfirmation(Order $order): void
    {
        // إرسال بريد إلكتروني
        // Mail::send('emails.order-confirmation', ['order' => $order], function ($m) use ($order) {
        //     $m->to($order->customer->email)->subject('تأكيد الطلب');
        // });

        // إرسال إشعار في التطبيق
        // Notification::send($order->customer, new OrderConfirmedNotification($order));
    }

    /**
     * إرسال QR عبر واتساب
     *
     * @param Order $order
     */
    public function sendWhatsAppQr(Order $order): void
    {
        if ($order->channel !== 'IN_STORE_PICKUP') {
            return;
        }

        $customer = $order->customer;
        $store = $order->pickupStore;
        $qrImage = app(QrService::class)->getQrImage($order);

        // إنشاء الرسالة
        $message = $this->buildWhatsAppMessage($order, $store);

        // إرسال عبر WhatsApp API
        // تم هذا الجزء في NotificationService بحسب API المستخدمة

        // مثال:
        // WhatsAppService::sendMessage(
        //     phone: $customer->phone,
        //     message: $message,
        //     imageUrl: $qrImage,
        // );

        // لأغراض التطوير، نسجل محاولة الإرسال
        \Log::info('WhatsApp QR Sent', [
            'order_id' => $order->id,
            'customer_phone' => $customer->phone,
            'qr_code' => $order->qrCode->qr_code,
        ]);
    }

    /**
     * بناء رسالة واتساب
     *
     * @param Order $order
     * @param InspectionCenter $store
     * @return string
     */
    private function buildWhatsAppMessage(Order $order, $store): string
    {
        $hoursLeft = $order->pickup_expiry->diffInHours(now());

        $message = "
أهلاً وسهلاً في خزانة! 👋

طلبك الجديد جاهز للاستلام ✅

🏷️ رقم الطلب: {$order->order_number}
🏪 المتجر: {$store->name}
📍 العنوان: {$store->address}
⏰ الساعات: " . ($store->pickup_hours ? json_encode($store->pickup_hours) : 'متاح') . "

⏳ الصلاحية: {$hoursLeft} ساعة فقط

عرض الكود أسفله عند الاستلام من المتجر 👇
";

        return $message;
    }

    /**
     * إرسال تذكير قبل انتهاء الصلاحية
     *
     * @param Order $order
     */
    public function sendPickupReminder(Order $order): void
    {
        if ($order->channel !== 'IN_STORE_PICKUP') {
            return;
        }

        $hoursLeft = $order->pickup_expiry->diffInHours(now());

        if ($hoursLeft <= 6 && $hoursLeft > 0) {
            $message = "
تذكير: طلبك سينتهي قريباً ⏰

📦 طلبك برقم {$order->order_number}
⏳ ينتهي في: {$hoursLeft} ساعة

تذكر: اجلب الكود الذي أرسلناه لك واستلم طلبك من المتجر 🏪
";

            // إرسال الرسالة
            $this->sendWhatsAppMessage($order->customer->phone, $message);
        }
    }

    /**
     * إرسال إشعار بانتهاء صلاحية الطلب
     *
     * @param Order $order
     */
    public function sendPickupExpiredNotification(Order $order): void
    {
        $message = "
آسفين! طلبك انتهت صلاحيته 😞

📦 طلب رقم: {$order->order_number}
📝 الحالة: منتهي الصلاحية

يمكنك إنشاء طلب جديد في أي وقت 😊
";

        $this->sendWhatsAppMessage($order->customer->phone, $message);
    }

    /**
     * إرسال إشعار بإكمال الطلب
     *
     * @param Order $order
     */
    public function sendPickupCompletedNotification(Order $order): void
    {
        $message = "
شكراً لاستخدام خزانة! 🎉

✅ تم استلام طلبك بنجاح
📦 رقم الطلب: {$order->order_number}

نتمنى أن يعجبك! 💚
";

        $this->sendWhatsAppMessage($order->customer->phone, $message);

        // إرسال بريد إلكتروني أيضاً
        // Mail::send('emails.pickup-completed', ['order' => $order], ...);
    }

    /**
     * إرسال رسالة واتساب
     *
     * @param string $phone
     * @param string $message
     */
    private function sendWhatsAppMessage(string $phone, string $message): void
    {
        // هنا يتم التكامل مع API واتساب مثل:
        // - Twilio
        // - MessageBird
        // - WhatsApp Business API

        \Log::info('WhatsApp Message Sent', [
            'phone' => $phone,
            'message' => $message,
        ]);
    }

    /**
     * إرسال تقرير يومي للمتجر
     *
     * @param InspectionCenter $store
     */
    public function sendDailyStoreReport($store): void
    {
        $stats = app(StorePickupService::class)->getStoreStatistics($store->id);

        $message = "
📊 تقرير متجر {$store->name}

📦 الطلبات المستلمة: {$stats['completed_orders']}
💰 الإيرادات: {$stats['total_revenue']} ريال
📈 متوسط العملية: {$stats['average_transaction']} ريال
";

        // إرسال للإدارة
        // Notification::send(Admin::all(), new DailyStoreReportNotification($message));
    }
}
