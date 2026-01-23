<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Item Card Component
 * 
 * Clean UI Component that prepares data and renders item card
 * Handles data transformation from different item types
 */
class ItemCard extends Component
{
    public function __construct(
        public $item,
        public string $variant = 'public',
        public ?string $url = null,
        public ?object $primaryImage = null,
        public $images = null,
        public ?string $title = null,
        public ?float $price = null,
        public ?float $displayPrice = null,
        public ?string $operationType = null,
        public ?string $condition = null,
        public ?string $category = null,
        public ?string $createdAt = null,
        public bool $showMeta = true,
        public bool $showImagePreview = true,
    ) {
        $this->prepareData();
    }

    /**
     * Prepare and normalize data from item
     */
    private function prepareData(): void
    {
        // Determine item type and extract data
        $isPublicItem = isset($this->item->primaryImage) 
            || (isset($this->item->operationType) && is_string($this->item->operationType));

        // URL
        if (!$this->url) {
            $this->url = $isPublicItem
                ? route('public.items.show', [
                    'id' => $this->item->id,
                    'slug' => $this->item->slug ?? null
                ])
                : route('items.show', $this->item);
        }

        // Primary Image
        if (!$this->primaryImage) {
            $this->primaryImage = $isPublicItem
                ? $this->item->primaryImage
                : ($this->item->images && $this->item->images->count() > 0
                    ? ($this->item->images->where('is_primary', true)->first() ?? $this->item->images->first())
                    : null);
        }

        // Images Collection
        if ($this->images === null) {
            $this->images = $isPublicItem
                ? ($this->item->images ?? collect())
                : ($this->item->images ?? collect());
        }

        // Title
        if (!$this->title) {
            $this->title = $this->item->title ?? '';
        }

        // Price
        if ($this->price === null) {
            $this->price = $isPublicItem
                ? ($this->item->price ?? null)
                : ($this->item->price ?? null);
        }

        // Operation Type
        if (!$this->operationType) {
            $this->operationType = $isPublicItem
                ? ($this->item->operationType ?? 'sell')
                : ($this->item->operation_type->value ?? 'sell');
        }

        // Display Price (with fees)
        if ($this->displayPrice === null && $this->price !== null) {
            $this->displayPrice = price_with_fee((float) $this->price, $this->operationType);
        }

        // Condition
        if ($this->condition === null) {
            $this->condition = $this->item->condition ?? null;
        }

        // Category
        if ($this->category === null) {
            $this->category = $isPublicItem
                ? ($this->item->category?->name ?? null)
                : ($this->item->category?->name ?? null);
        }

        // Created At
        if ($this->createdAt === null) {
            $this->createdAt = $isPublicItem
                ? ($this->item->createdAtFormatted ?? null)
                : null;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('partials.item-card', [
            'item' => $this->item,
            'variant' => $this->variant,
            'url' => $this->url,
            'primaryImage' => $this->primaryImage,
            'images' => $this->images,
            'title' => $this->title,
            'price' => $this->price,
            'displayPrice' => $this->displayPrice,
            'operationType' => $this->operationType,
            'condition' => $this->condition,
            'category' => $this->category,
            'createdAt' => $this->createdAt,
            'showMeta' => $this->showMeta,
            'showImagePreview' => $this->showImagePreview,
        ]);
    }
}
