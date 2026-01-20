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
     * Note: Named approvalRelation to avoid conflict with interface method
     */
    public function approvalRelation(): MorphOne
    {
        return $this->morphOne(Approval::class, 'approvable');
    }

    /**
     * Get the approval record for this model
     * Required by Approvable interface
     */
    public function approval(): ?Approval
    {
        // Check if relationship is already loaded
        if ($this->relationLoaded('approvalRelation')) {
            return $this->getRelation('approvalRelation');
        }
        
        return $this->approvalRelation()->first();
    }

    /**
     * Check if the model is approved
     */
    public function isApproved(): bool
    {
        return $this->approval()?->status === ApprovalStatus::APPROVED;
    }

    /**
     * Check if the model is pending approval
     */
    public function isPending(): bool
    {
        return $this->approval()?->status === ApprovalStatus::PENDING;
    }

    /**
     * Check if the model is rejected
     */
    public function isRejected(): bool
    {
        return $this->approval()?->status === ApprovalStatus::REJECTED;
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
        return $this->approval()?->status;
    }

    /**
     * Scope to get only approved records
     */
    public function scopeApproved($query)
    {
        return $query->whereHas('approvalRelation', function ($q) {
            $q->where('status', ApprovalStatus::APPROVED->value);
        });
    }

    /**
     * Scope to get only pending records
     */
    public function scopePending($query)
    {
        return $query->whereHas('approvalRelation', function ($q) {
            $q->where('status', ApprovalStatus::PENDING->value);
        });
    }

    /**
     * Scope to get only rejected records
     */
    public function scopeRejected($query)
    {
        return $query->whereHas('approvalRelation', function ($q) {
            $q->where('status', ApprovalStatus::REJECTED->value);
        });
    }
}
