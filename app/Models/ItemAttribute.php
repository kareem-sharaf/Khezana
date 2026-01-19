<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ItemAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'attributable_type',
        'attributable_id',
        'attribute_id',
        'value',
    ];

    /**
     * Get the parent attributable model.
     */
    public function attributable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the attribute.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}
