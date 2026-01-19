<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\ApprovalStatus;
use App\Models\Approval;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait that provides approval functionality to models
 */
trait HasApproval
{
    /**
     * Get the approval relationship
     */
    public function approval(): MorphOne
    {
        return $this->morphOne(Approval::class, 'approvable');
    }

    /**
     * Check if the model is approved
     */
    public function isApproved(): bool
    {
        return $this->approval?->status === ApprovalStatus::APPROVED;
    }

    /**
     * Check if the model is pending approval
     */
    public function isPending(): bool
    {
        return $this->approval?->status === ApprovalStatus::PENDING;
    }

    /**
     * Check if the model is rejected
     */
    public function isRejected(): bool
    {
        return $this->approval?->status === ApprovalStatus::REJECTED;
    }

    /**
     * Check if the model can be published (is approved)
     */
    public function canBePublished(): bool
    {
        return $this->isApproved();
    }

    /**
     * Get the current approval status
     */
    public function getApprovalStatus(): ?ApprovalStatus
    {
        return $this->approval?->status;
    }

    /**
     * Scope to get only approved records
     */
    public function scopeApproved($query)
    {
        return $query->whereHas('approval', function ($q) {
            $q->where('status', ApprovalStatus::APPROVED->value);
        });
    }

    /**
     * Scope to get only pending records
     */
    public function scopePending($query)
    {
        return $query->whereHas('approval', function ($q) {
            $q->where('status', ApprovalStatus::PENDING->value);
        });
    }

    /**
     * Scope to get only rejected records
     */
    public function scopeRejected($query)
    {
        return $query->whereHas('approval', function ($q) {
            $q->where('status', ApprovalStatus::REJECTED->value);
        });
    }
}
