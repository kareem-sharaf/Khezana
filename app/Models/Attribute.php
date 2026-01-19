<?php

namespace App\Models;

use App\Enums\AttributeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_required',
    ];

    protected $casts = [
        'type' => AttributeType::class,
        'is_required' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Attribute $attribute) {
            if (empty($attribute->slug)) {
                $attribute->slug = Str::slug($attribute->name);
            }
        });

        static::updating(function (Attribute $attribute) {
            if ($attribute->isDirty('name') && empty($attribute->slug)) {
                $attribute->slug = Str::slug($attribute->name);
            }
        });
    }

    /**
     * Get categories that have this attribute.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_attribute')
            ->withTimestamps();
    }

    /**
     * Get predefined values for this attribute (for select type).
     */
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * Check if attribute is of select type.
     */
    public function isSelectType(): bool
    {
        return $this->type === AttributeType::SELECT;
    }

    /**
     * Check if attribute is of text type.
     */
    public function isTextType(): bool
    {
        return $this->type === AttributeType::TEXT;
    }

    /**
     * Check if attribute is of number type.
     */
    public function isNumberType(): bool
    {
        return $this->type === AttributeType::NUMBER;
    }

    /**
     * Scope: Get attributes by type.
     */
    public function scopeOfType($query, AttributeType $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Get required attributes.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }
}
