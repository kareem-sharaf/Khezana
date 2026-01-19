<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Approvable;
use App\Traits\HasApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ClothingRequest Model (Example - Requesting Clothes)
 * 
 * This is an example model showing how to use the Approval module
 */
class ClothingRequest extends Model implements Approvable
{
    use HasFactory, HasApproval;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'size',
        'status',
        // ... other fields
    ];

    /**
     * Get the user who made the request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created/submitted this request
     * Required by Approvable interface
     */
    public function getSubmitter(): ?User
    {
        return $this->user;
    }

    /**
     * Get the title/name for display in approval lists
     * Required by Approvable interface
     */
    public function getApprovalTitle(): string
    {
        return $this->title ?? 'Untitled Request';
    }

    /**
     * Get the type identifier for polymorphic relationships
     * Required by Approvable interface
     */
    public function getApprovalType(): string
    {
        return self::class;
    }

    /**
     * Scope to get only published (approved) requests
     */
    public function scopePublished($query)
    {
        return $query->approved();
    }
}
