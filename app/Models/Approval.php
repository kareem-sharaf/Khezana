<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Approvable;
use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Approval Model
 *
 * Represents an approval record for any approvable entity (Product, Request, etc.)
 */
class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'status',
        'submitted_by',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'resubmission_count',
    ];

    protected $casts = [
        'status' => ApprovalStatus::class,
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot(): void
    {
        parent::boot();

        // Fix legacy 'request' type when models are loaded
        static::retrieved(function (Approval $approval) {
            if (isset($approval->attributes['approvable_type']) && $approval->attributes['approvable_type'] === 'request') {
                $approval->attributes['approvable_type'] = Request::class;
            }
        });
    }

    /**
     * Get the approvable entity (Product, Request, etc.)
     */
    public function approvable(): MorphTo
    {
        // Fix legacy 'request' type before morphTo() reads it
        // morphTo() reads directly from $this->attributes, so we need to fix it here
        if (isset($this->attributes['approvable_type']) && $this->attributes['approvable_type'] === 'request') {
            $this->attributes['approvable_type'] = Request::class;
        }

        return $this->morphTo();
    }

    /**
     * Get the user who submitted the content
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the admin who reviewed the content
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, ApprovalStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter pending approvals
     */
    public function scopePending($query)
    {
        return $query->where('status', ApprovalStatus::PENDING);
    }

    /**
     * Scope to filter approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', ApprovalStatus::APPROVED);
    }

    /**
     * Scope to filter rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status', ApprovalStatus::REJECTED);
    }

    /**
     * Scope to filter archived
     */
    public function scopeArchived($query)
    {
        return $query->where('status', ApprovalStatus::ARCHIVED);
    }

    /**
     * Check if approval is pending
     */
    public function isPending(): bool
    {
        return $this->status === ApprovalStatus::PENDING;
    }

    /**
     * Check if approval is approved
     */
    public function isApproved(): bool
    {
        return $this->status === ApprovalStatus::APPROVED;
    }

    /**
     * Check if approval is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === ApprovalStatus::REJECTED;
    }

    /**
     * Check if approval is archived
     */
    public function isArchived(): bool
    {
        return $this->status === ApprovalStatus::ARCHIVED;
    }
}
