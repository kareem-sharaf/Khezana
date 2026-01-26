<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderQrCode;
use App\Services\OrderService;
use App\Services\NotificationService;
use App\Services\QrService;
use Illuminate\Console\Command;

/**
 * CheckExpiredOrders Command
 *
 * مهمة مجدولة تتحقق من الطلبات المنتهية الصلاحية وتحدثها
 * يتم تشغيلها كل 5 دقائق
 */
class CheckExpiredOrders extends Command
{
    protected $signature = 'orders:check-expired';
    protected $description = 'التحقق من الطلبات المنتهية الصلاحية وتحديث حالتها';

    public function __construct(
        protected OrderService $orderService,
        protected QrService $qrService,
        protected NotificationService $notificationService,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('🔍 جاري التحقق من الطلبات المنتهية الصلاحية...');

        try {
            // البحث عن الطلبات المنتهية
            $expiredOrders = Order::where('channel', 'IN_STORE_PICKUP')
                ->where('status', 'CONFIRMED')
                ->where('pickup_expiry', '<', now())
                ->get();

            if ($expiredOrders->isEmpty()) {
                $this->info('✅ لا توجد طلبات منتهية الصلاحية');
                return;
            }

            $this->info("⏳ وجدنا {$expiredOrders->count()} طلب منتهي الصلاحية");

            foreach ($expiredOrders as $order) {
                try {
                    // 1. تغيير حالة الطلب إلى EXPIRED
                    $order->update(['status' => 'EXPIRED']);

                    // 2. إعادة المنتجات إلى AVAILABLE
                    $this->orderService->releaseItems($order);

                    // 3. تعليم QR كمنتهي
                    if ($order->qrCode) {
                        $this->qrService->expireQrCode($order->qrCode);
                    }

                    // 4. تسجيل الحدث
                    $order->tracking()->create([
                        'old_status' => 'CONFIRMED',
                        'new_status' => 'EXPIRED',
                        'event_type' => 'EXPIRATION',
                        'actor_type' => 'SYSTEM',
                        'notes' => 'انتهت صلاحية الحجز تلقائياً',
                    ]);

                    // 5. إشعار العميل
                    $this->notificationService->sendPickupExpiredNotification($order);

                    $this->info("✅ تم تحديث الطلب #{$order->order_number}");
                } catch (\Exception $e) {
                    $this->error("❌ خطأ في تحديث الطلب #{$order->order_number}: {$e->getMessage()}");
                }
            }

            $this->info('✅ انتهت عملية التحقق');
        } catch (\Exception $e) {
            $this->error("❌ خطأ عام: {$e->getMessage()}");
        }
    }
}
