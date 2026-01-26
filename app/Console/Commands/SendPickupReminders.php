<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Console\Command;

/**
 * SendPickupReminders Command
 *
 * مهمة مجدولة ترسل تذكيرات للعملاء قبل انتهاء الصلاحية
 * يتم تشغيلها كل ساعة
 */
class SendPickupReminders extends Command
{
    protected $signature = 'notifications:send-pickup-reminders';
    protected $description = 'إرسال تذكيرات للعملاء قبل انتهاء صلاحية الاستلام';

    public function __construct(
        protected NotificationService $notificationService,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('📢 جاري إرسال التذكيرات...');

        try {
            // البحث عن الطلبات القريبة من الانتهاء (أقل من 6 ساعات)
            $upcomingOrders = Order::where('channel', 'IN_STORE_PICKUP')
                ->where('status', 'CONFIRMED')
                ->where('pickup_expiry', '>', now())
                ->where('pickup_expiry', '<=', now()->addHours(6))
                ->get();

            if ($upcomingOrders->isEmpty()) {
                $this->info('✅ لا توجد طلبات تحتاج تذكيرات');
                return;
            }

            $this->info("⏰ سيتم إرسال {$upcomingOrders->count()} تذكير");

            foreach ($upcomingOrders as $order) {
                try {
                    $this->notificationService->sendPickupReminder($order);
                    $this->info("✅ تم إرسال تذكير للطلب #{$order->order_number}");
                } catch (\Exception $e) {
                    $this->error("❌ خطأ في إرسال تذكير للطلب #{$order->order_number}: {$e->getMessage()}");
                }
            }

            $this->info('✅ انتهت عملية الإرسال');
        } catch (\Exception $e) {
            $this->error("❌ خطأ عام: {$e->getMessage()}");
        }
    }
}
