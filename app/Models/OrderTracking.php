<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * OrderTracking Model
 *
 * يتتبع جميع التغييرات والأحداث المتعلقة بالطلب
 *
 * @property int $id
 * @property int $order_id معرف الطلب
 * @property string $old_status الحالة السابقة
 * @property string $new_status الحالة الجديدة
 * @property string $event_type نوع الحدث
 * @property string $notes ملاحظات
 * @property int $actor_id معرف الممثل
 * @property string $actor_type نوع الممثل
 */
class OrderTracking extends Model
{
    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'event_type',
        'notes',
        'actor_id',
        'actor_type',
    ];

    protected $casts = [
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
     * العلاقة مع المستخدم الممثل
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
