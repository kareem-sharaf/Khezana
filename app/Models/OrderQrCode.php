<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * OrderQrCode Model
 *
 * يمثل رمز QR لطلب الاستلام من المتجر
 *
 * @property int $id
 * @property int $order_id معرف الطلب
 * @property string $qr_code رمز QR الفريد
 * @property string $qr_data بيانات QR المشفرة
 * @property string $status حالة الرمز
 * @property datetime $used_at وقت استخدام الرمز
 * @property int $scanned_by_user_id معرف الموظف الذي مسح الرمز
 * @property datetime $scanned_at وقت المسح
 * @property datetime $expiry_date تاريخ انتهاء الصلاحية
 */
class OrderQrCode extends Model
{
    protected $fillable = [
        'order_id',
        'qr_code',
        'qr_data',
        'status',
        'used_at',
        'scanned_by_user_id',
        'scanned_at',
        'expiry_date',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'scanned_at' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    /**
     * العلاقة مع الطلب
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * العلاقة مع موظف المتجر
     */
    public function scannedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by_user_id');
    }

    /**
     * التحقق من صلاحية الرمز
     */
    public function isValid(): bool
    {
        return $this->status === 'ACTIVE' &&
            $this->expiry_date->isFuture();
    }

    /**
     * التحقق من استخدام الرمز
     */
    public function isUsed(): bool
    {
        return $this->status === 'USED';
    }

    /**
     * التحقق من انتهاء صلاحية الرمز
     */
    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    /**
     * تعليم الرمز كمستخدم
     */
    public function markAsUsed(int $userId): void
    {
        $this->update([
            'status' => 'USED',
            'used_at' => now(),
            'scanned_by_user_id' => $userId,
            'scanned_at' => now(),
        ]);
    }
}
