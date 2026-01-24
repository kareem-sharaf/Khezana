<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ItemImage Model
 * 
 * Represents an image associated with an item
 */
class ItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'path',
        'path_webp',
        'disk',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the item that owns this image
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Boot the model
     */
    protected static function boot(): void
    {
        parent::boot();

        // Ensure only one primary image per item
        static::creating(function (ItemImage $image) {
            if ($image->is_primary) {
                ItemImage::where('item_id', $image->item_id)
                    ->update(['is_primary' => false]);
            }
        });

        static::updating(function (ItemImage $image) {
            if ($image->is_primary && $image->isDirty('is_primary')) {
                ItemImage::where('item_id', $image->item_id)
                    ->where('id', '!=', $image->id)
                    ->update(['is_primary' => false]);
            }
        });
    }
}
