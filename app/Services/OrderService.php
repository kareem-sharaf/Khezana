<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Item;
use App\Events\OrderCreated;
use App\Events\OrderCancelled;
use Illuminate\Support\Facades\DB;

/**
 * OrderService
 *
 * خدمة معالجة الطلبات - التحقق من صحة الطلبات، حساب الأسعار، حجز المنتجات
 */
class OrderService
{
    /**
     * إنشاء طلب جديد
     *
     * @param array $data بيانات الطلب
     * @return Order الطلب المنشأ
     * @throws \Exception
     */
    public function createOrder(array $data): Order
    {
        // التحقق من صحة البيانات
        $this->validateOrder($data);

        return DB::transaction(function () use ($data) {
            // 1. إنشاء الطلب
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => auth()->id(),
                'channel' => $data['channel'] ?? 'DELIVERY',
                'status' => 'CREATED',
                'delivery_address' => $data['delivery_address'] ?? null,
                'delivery_city' => $data['delivery_city'] ?? null,
                'delivery_notes' => $data['delivery_notes'] ?? null,
                'pickup_store_id' => $data['pickup_store_id'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'CASH_ON_DELIVERY',
            ]);

            // 2. إضافة عناصر الطلب
            $totalAmount = 0;
            foreach ($data['items'] as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);

                // التحقق من توفر المنتج
                if (!$this->isItemAvailable($item)) {
                    throw new \Exception("المنتج {$item->title} غير متاح");
                }

                $itemTotal = $this->calculateItemPrice($itemData);
                $totalAmount += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $itemData['item_id'],
                    'operation_type' => $itemData['operation_type'] ?? 'SALE',
                    'unit_price' => $item->price,
                    'rent_start_date' => $itemData['rent_start_date'] ?? null,
                    'rent_end_date' => $itemData['rent_end_date'] ?? null,
                    'rent_days' => $itemData['rent_days'] ?? null,
                    'rent_price_per_day' => $itemData['rent_price_per_day'] ?? null,
                    'deposit_amount' => $itemData['deposit_amount'] ?? 0,
                    'item_total' => $itemTotal,
                ]);
            }

            // 3. تحديث الإجمالي
            $order->update([
                'total_amount' => $totalAmount,
                'delivery_fee' => $data['delivery_fee'] ?? 0,
            ]);

            // 4. حجز المنتجات
            $this->reserveItems($order);

            // 5. للاستلام من المتجر: توليد QR
            if ($order->channel === 'IN_STORE_PICKUP') {
                app(QrService::class)->generateQrCode($order);

                // تعيين تاريخ انتهاء الصلاحية (24 ساعة)
                $order->update([
                    'pickup_expiry' => now()->addHours(24),
                    'status' => 'CONFIRMED',
                ]);
            } else {
                $order->update(['status' => 'PENDING_PAYMENT']);
            }

            // 6. إطلاق حدث
            OrderCreated::dispatch($order);

            return $order;
        });
    }

    /**
     * التحقق من صحة بيانات الطلب
     *
     * @param array $data
     * @throws \Exception
     */
    public function validateOrder(array &$data): void
    {
        if (empty($data['items'])) {
            throw new \Exception('يجب تحديد عناصر الطلب');
        }

        if ($data['channel'] === 'DELIVERY') {
            if (empty($data['delivery_address'])) {
                throw new \Exception('عنوان التوصيل مطلوب');
            }
            if (empty($data['delivery_city'])) {
                throw new \Exception('المدينة مطلوبة');
            }
        } elseif ($data['channel'] === 'IN_STORE_PICKUP') {
            if (empty($data['pickup_store_id'])) {
                throw new \Exception('يجب اختيار متجر للاستلام');
            }
        }
    }

    /**
     * التحقق من توفر المنتج
     *
     * @param Item $item
     * @return bool
     */
    public function isItemAvailable(Item $item): bool
    {
        return $item->item_status === 'AVAILABLE' && $item->is_available;
    }

    /**
     * حساب سعر العنصر
     *
     * @param array $itemData
     * @return decimal
     */
    public function calculateItemPrice(array $itemData): decimal
    {
        $operationType = $itemData['operation_type'] ?? 'SALE';

        if ($operationType === 'RENT') {
            $rentDays = $itemData['rent_days'] ?? 1;
            $pricePerDay = $itemData['rent_price_per_day'] ?? 0;
            $deposit = $itemData['deposit_amount'] ?? 0;
            return ($rentDays * $pricePerDay) + $deposit;
        } elseif ($operationType === 'DONATE') {
            return 0;
        }

        return $itemData['unit_price'] ?? 0;
    }

    /**
     * حجز المنتجات
     *
     * @param Order $order
     */
    public function reserveItems(Order $order): void
    {
        foreach ($order->items as $orderItem) {
            $orderItem->item->update([
                'item_status' => 'RESERVED',
                'reservation_expiry' => now()->addHours(24),
            ]);
        }
    }

    /**
     * إطلاق المنتجات (عند الإلغاء أو انتهاء الصلاحية)
     *
     * @param Order $order
     */
    public function releaseItems(Order $order): void
    {
        foreach ($order->items as $orderItem) {
            $orderItem->item->update([
                'item_status' => 'AVAILABLE',
                'reservation_expiry' => null,
            ]);
        }
    }

    /**
     * إلغاء طلب
     *
     * @param Order $order
     * @param string $reason
     * @throws \Exception
     */
    public function cancelOrder(Order $order, string $reason = ''): void
    {
        if (!$order->canBeCancelled()) {
            throw new \Exception('لا يمكن إلغاء هذا الطلب في الحالة الحالية');
        }

        DB::transaction(function () use ($order, $reason) {
            // إطلاق المنتجات
            $this->releaseItems($order);

            // تحديث حالة الطلب
            $order->update(['status' => 'CANCELLED']);

            // تسجيل الحدث
            $order->tracking()->create([
                'old_status' => 'CONFIRMED',
                'new_status' => 'CANCELLED',
                'event_type' => 'CANCELLATION',
                'notes' => $reason,
                'actor_id' => auth()->id(),
                'actor_type' => 'CUSTOMER',
            ]);

            // إطلاق الحدث
            OrderCancelled::dispatch($order, $reason);
        });
    }

    /**
     * الحصول على تفاصيل الطلب
     *
     * @param int $orderId
     * @return Order|null
     */
    public function getOrderDetails(int $orderId): ?Order
    {
        return Order::with(['items.item', 'items.item.images', 'customer', 'pickupStore'])
            ->findOrFail($orderId);
    }

    /**
     * الحصول على طلبات المستخدم
     *
     * @param int $userId
     * @return \Illuminate\Pagination\Paginate
     */
    public function getUserOrders(int $userId)
    {
        return Order::where('customer_id', $userId)
            ->with(['items.item', 'pickupStore'])
            ->latest()
            ->paginate(15);
    }
}
