<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * OrderItem Model
 *
 * يمثل عنصراً واحداً من عناصر الطلب
 *
 * @property int $id
 * @property int $order_id معرف الطلب
 * @property int $item_id معرف المنتج
 * @property string $operation_type نوع العملية (RENT, SALE, DONATE)
 * @property datetime $rent_start_date تاريخ بداية الإيجار
 * @property datetime $rent_end_date تاريخ نهاية الإيجار
 * @property int $rent_days عدد أيام الإيجار
 * @property decimal $unit_price سعر الوحدة
 * @property decimal $rent_price_per_day سعر الإيجار اليومي
 * @property decimal $deposit_amount مبلغ الضمان
 * @property decimal $item_total إجمالي سعر العنصر
 * @property string $item_status حالة العنصر
 */
class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'item_id',
        'operation_type',
        'rent_start_date',
        'rent_end_date',
        'rent_days',
        'unit_price',
        'rent_price_per_day',
        'deposit_amount',
        'item_total',
        'item_status',
    ];

    protected $casts = [
        'rent_start_date' => 'date',
        'rent_end_date' => 'date',
        'unit_price' => 'decimal:2',
        'rent_price_per_day' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'item_total' => 'decimal:2',
    ];

    /**
     * العلاقة مع الطلب
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * العلاقة مع المنتج
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * حساب إجمالي السعر
     */
    public function calculateTotal(): decimal
    {
        if ($this->operation_type === 'RENT') {
            return ($this->rent_price_per_day * $this->rent_days) + $this->deposit_amount;
        }

        return $this->unit_price;
    }

    /**
     * التحقق من إمكانية استرجاع العنصر
     */
    public function canBeReturned(): bool
    {
        return $this->operation_type === 'RENT' &&
            $this->item_status === 'IN_USE';
    }
}
