<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Approval;

/**
 * Contract for models that can be submitted for approval
 */
interface Approvable
{
    /**
     * Get the approval record for this model
     */
    public function approval(): ?Approval;

    /**
     * Check if the model is approved
     */
    public function isApproved(): bool;

    /**
     * Check if the model is pending approval
     */
    public function isPending(): bool;

    /**
     * Check if the model is rejected
     */
    public function isRejected(): bool;

    /**
     * Check if the model can be published (is approved)
     */
    public function canBePublished(): bool;

    /**
     * Get the user who created/submitted this model
     */
    public function getSubmitter(): ?\App\Models\User;

    /**
     * Get the title/name for display in approval lists
     */
    public function getApprovalTitle(): string;

    /**
     * Get the type identifier for polymorphic relationships
     */
    public function getApprovalType(): string;
}
