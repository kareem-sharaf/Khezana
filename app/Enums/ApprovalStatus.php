<?php

declare(strict_types=1);

namespace App\Enums;

enum ApprovalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case ARCHIVED = 'archived';
    case VERIFICATION_REQUIRED = 'verification_required';

    /**
     * Get all status values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get human-readable label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('approvals.statuses.pending'),
            self::APPROVED => __('approvals.statuses.approved'),
            self::REJECTED => __('approvals.statuses.rejected'),
            self::ARCHIVED => __('approvals.statuses.archived'),
            self::VERIFICATION_REQUIRED => __('approvals.statuses.verification_required'),
        };
    }

    /**
     * Get badge color for Filament UI
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::ARCHIVED => 'gray',
            self::VERIFICATION_REQUIRED => 'info',
        };
    }

    /**
     * Check if status is pending
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if status is approved
     */
    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    /**
     * Check if status is rejected
     */
    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }

    /**
     * Check if status is archived
     */
    public function isArchived(): bool
    {
        return $this === self::ARCHIVED;
    }

    /**
     * Check if status requires verification at branch
     */
    public function isVerificationRequired(): bool
    {
        return $this === self::VERIFICATION_REQUIRED;
    }

    /**
     * Check if content can be published (approved)
     */
    public function canBePublished(): bool
    {
        return $this === self::APPROVED;
    }
}
