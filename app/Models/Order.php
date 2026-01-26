<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Order Model
 *
 * يمثل طلب العميل سواء للتوصيل أو الاستلام من المتجر
 *
 * @property int $id
 * @property string $order_number الرقم الفريد للطلب
 * @property int $customer_id معرف العميل
 * @property string $channel طريقة الاستلام (DELIVERY, IN_STORE_PICKUP)
 * @property string $status حالة الطلب
 * @property decimal $total_amount إجمالي المبلغ
 * @property decimal $delivery_fee رسوم التوصيل
 * @property string $delivery_address عنوان التوصيل
 * @property string $delivery_city المدينة
 * @property int $pickup_store_id معرف المتجر
 * @property string $pickup_code رمز الاستلام
 * @property datetime $pickup_expiry انتهاء صلاحية الاستلام
 * @property string $payment_method طريقة الدفع
 * @property string $payment_status حالة الدفع
 */
class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'channel',
        'status',
        'total_amount',
        'delivery_fee',
        'delivery_address',
        'delivery_city',
        'delivery_notes',
        'pickup_store_id',
        'pickup_code',
        'pickup_expiry',
        'payment_method',
        'payment_status',
        'pickup_scheduled_date',
        'pickup_actual_date',
        'notes',
    ];

    protected $casts = [
        'pickup_expiry' => 'datetime',
        'pickup_scheduled_date' => 'date',
        'pickup_actual_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
    ];

    /**
     * العلاقة مع العميل
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * العلاقة مع المتجر
     */
    public function pickupStore(): BelongsTo
    {
        return $this->belongsTo(InspectionCenter::class, 'pickup_store_id');
    }

    /**
     * العلاقة مع عناصر الطلب
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * العلاقة مع رمز QR
     */
    public function qrCode(): HasOne
    {
        return $this->hasOne(OrderQrCode::class);
    }

    /**
     * العلاقة مع سجل التتبع
     */
    public function tracking(): HasMany
    {
        return $this->hasMany(OrderTracking::class);
    }

    /**
     * العلاقة مع المعاملات
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(StoreTransaction::class);
    }

    /**
     * التحقق من كون الطلب جاهزاً للاستلام
     */
    public function isReadyForPickup(): bool
    {
        return $this->channel === 'IN_STORE_PICKUP' &&
            in_array($this->status, ['READY_FOR_PICKUP', 'CONFIRMED']);
    }

    /**
     * التحقق من انتهاء صلاحية الطلب
     */
    public function isExpired(): bool
    {
        return $this->pickup_expiry && $this->pickup_expiry->isPast();
    }

    /**
     * التحقق من إمكانية إلغاء الطلب
     */
    public function canBeCancelled(): bool
    {
        $cancellableStatuses = ['CREATED', 'PENDING_PAYMENT', 'CONFIRMED', 'PROCESSING'];
        return in_array($this->status, $cancellableStatuses);
    }

    /**
     * توليد رقم طلب فريد
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . date('Ymd');
        $lastOrder = self::where('order_number', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $number = $lastOrder ?
            intval(substr($lastOrder->order_number, -5)) + 1 : 1;

        return $prefix . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
