<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function (Category $category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('name');
    }

    /**
     * Get all descendants (recursive).
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors (recursive).
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors->reverse();
    }

    /**
     * Get attributes assigned to this category.
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'category_attribute')
            ->withTimestamps();
    }

    /**
     * Get all attributes including from parent categories.
     */
    public function getAllAttributes()
    {
        // Get attributes from the relationship, not the model's attributes property
        $attributes = $this->relationLoaded('attributes') 
            ? $this->attributes 
            : $this->attributes()->get();

        // Ensure we have a Collection of Models
        if (!$attributes instanceof \Illuminate\Support\Collection) {
            $attributes = collect($attributes);
        }

        // Filter to ensure all items are Attribute models
        $attributes = $attributes->filter(function($item) {
            return $item instanceof \App\Models\Attribute;
        });

        if ($this->parent) {
            $parentAttributes = $this->parent->getAllAttributes();
            // Ensure parent attributes is also a Collection
            if (!$parentAttributes instanceof \Illuminate\Support\Collection) {
                $parentAttributes = collect($parentAttributes);
            }
            // Filter to ensure all items are Attribute models
            $parentAttributes = $parentAttributes->filter(function($item) {
                return $item instanceof \App\Models\Attribute;
            });
            $attributes = $attributes->merge($parentAttributes);
        }

        // Use unique with callback to ensure we get unique Attribute models
        return $attributes->unique(function($attribute) {
            return $attribute->id;
        })->values();
    }

    /**
     * Get all required attributes including from parent categories.
     */
    public function getAllRequiredAttributes()
    {
        return $this->getAllAttributes()->filter(function($attribute) {
            return $attribute->is_required === true;
        });
    }

    /**
     * Check if category has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Check if category is root (no parent).
     */
    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Get the full path of the category (e.g., "Men > Suits").
     */
    public function getFullPath(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Scope: Get only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get only root categories.
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
