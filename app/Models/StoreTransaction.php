<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * StoreTransaction Model
 *
 * يسجل جميع المعاملات التي تحدث في المتجر (مسح QR، دفع، إلخ)
 *
 * @property int $id
 * @property int $order_id معرف الطلب
 * @property int $store_id معرف المتجر
 * @property int $staff_user_id معرف الموظف
 * @property string $transaction_type نوع المعاملة
 * @property decimal $amount المبلغ
 * @property string $payment_method طريقة الدفع
 * @property string $notes ملاحظات
 */
class StoreTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'store_id',
        'staff_user_id',
        'transaction_type',
        'amount',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * العلاقة مع الطلب
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * العلاقة مع المتجر
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(InspectionCenter::class, 'store_id');
    }

    /**
     * العلاقة مع الموظف
     */
    public function staffUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }
}
