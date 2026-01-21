<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AdminActionLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_actions_logs';

    protected $fillable = [
        'admin_id',
        'action_type',
        'target_type',
        'target_id',
        'notes',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the admin who performed the action.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the target model.
     */
    public function target(): MorphTo
    {
        return $this->morphTo('target');
    }

    /**
     * Scope to filter by action type.
     */
    public function scopeByActionType($query, string $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope to filter by target type.
     */
    public function scopeByTargetType($query, string $targetType)
    {
        return $query->where('target_type', $targetType);
    }
}
