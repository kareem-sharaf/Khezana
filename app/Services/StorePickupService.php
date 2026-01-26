<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderQrCode;
use App\Models\StoreTransaction;
use Illuminate\Support\Facades\DB;
use App\Events\OrderPickupCompleted;

/**
 * StorePickupService
 *
 * خدمة معالجة عمليات الاستلام من المتجر
 */
class StorePickupService
{
    /**
     * التحقق من صحة QR في المتجر
     *
     * @param string $qrCode
     * @return array
     * @throws \Exception
     */
    public function verifyQrCode(string $qrCode): array
    {
        // التحقق من الرمز
        $qrModel = app(QrService::class)->validateQrCode($qrCode);

        if (!$qrModel) {
            throw new \Exception('رمز QR غير صحيح أو منتهي الصلاحية');
        }

        $order = $qrModel->order;

        return [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer->name,
            'customer_phone' => $order->customer->phone,
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->item->title,
                    'image' => $item->item->images->first()?->path,
                    'operation_type' => $item->operation_type,
                    'price' => $item->item_total,
                ];
            }),
            'total_amount' => $order->total_amount,
            'payment_method' => $order->payment_method,
        ];
    }

    /**
     * إكمال عملية الاستلام
     *
     * @param Order $order
     * @param int $staffUserId
     * @param array $paymentData
     * @return Order
     * @throws \Exception
     */
    public function completePickup(Order $order, int $staffUserId, array $paymentData = []): Order
    {
        return DB::transaction(function () use ($order, $staffUserId, $paymentData) {
            // التحقق من أن الطلب جاهز
            if ($order->channel !== 'IN_STORE_PICKUP' || !$order->isReadyForPickup()) {
                throw new \Exception('الطلب غير جاهز للاستلام');
            }

            // تحديث حالة الطلب
            $order->update([
                'status' => 'CUSTOMER_ARRIVED',
                'pickup_actual_date' => now(),
                'payment_status' => 'PAID',
            ]);

            // تحديث حالة QR
            $order->qrCode->markAsUsed($staffUserId);

            // تسجيل المعاملة
            StoreTransaction::create([
                'order_id' => $order->id,
                'store_id' => $order->pickup_store_id,
                'staff_user_id' => $staffUserId,
                'transaction_type' => 'PICKUP_SCAN',
                'amount' => $paymentData['amount'] ?? $order->total_amount,
                'payment_method' => $paymentData['payment_method'] ?? $order->payment_method,
                'notes' => $paymentData['notes'] ?? null,
            ]);

            // تحديث حالة المنتجات
            foreach ($order->items as $item) {
                if ($item->operation_type === 'RENT') {
                    $item->update(['item_status' => 'IN_USE']);
                } else {
                    $item->update(['item_status' => 'PICKED_UP']);
                }

                // تحديث حالة المنتج الأساسي
                $item->item->update([
                    'item_status' => $item->operation_type === 'RENT' ? 'RENTED' : 'SOLD',
                ]);
            }

            // تسجيل الحدث
            $order->tracking()->create([
                'old_status' => 'READY_FOR_PICKUP',
                'new_status' => 'CUSTOMER_ARRIVED',
                'event_type' => 'PICKUP_COMPLETED',
                'notes' => 'تم استلام الطلب من المتجر',
                'actor_id' => $staffUserId,
                'actor_type' => 'STAFF',
            ]);

            // تحديث حالة الطلب النهائية
            $order->update(['status' => 'COMPLETED']);

            // إطلاق الحدث
            OrderPickupCompleted::dispatch($order);

            return $order->fresh();
        });
    }

    /**
     * الحصول على طلبات الاستلام المعلقة للمتجر
     *
     * @param int $storeId
     * @return \Illuminate\Pagination\Paginate
     */
    public function getPendingPickups(int $storeId)
    {
        return Order::where('pickup_store_id', $storeId)
            ->where('channel', 'IN_STORE_PICKUP')
            ->whereIn('status', ['CONFIRMED', 'READY_FOR_PICKUP'])
            ->with(['items.item', 'customer'])
            ->latest()
            ->paginate(15);
    }

    /**
     * الحصول على إحصائيات المتجر
     *
     * @param int $storeId
     * @param string $date
     * @return array
     */
    public function getStoreStatistics(int $storeId, string $date = null): array
    {
        $date = $date ? \Carbon\Carbon::parse($date) : now();

        $completedOrders = Order::where('pickup_store_id', $storeId)
            ->where('status', 'COMPLETED')
            ->whereDate('pickup_actual_date', $date)
            ->count();

        $totalRevenue = StoreTransaction::where('store_id', $storeId)
            ->where('transaction_type', 'PICKUP_SCAN')
            ->whereDate('created_at', $date)
            ->sum('amount');

        return [
            'completed_orders' => $completedOrders,
            'total_revenue' => $totalRevenue,
            'average_transaction' => $completedOrders > 0 ? $totalRevenue / $completedOrders : 0,
        ];
    }
}
