<?php

declare(strict_types=1);

namespace App\ViewModels\Items;

use Illuminate\Support\Collection;

/**
 * Item Card ViewModel
 * 
 * Prepares all data needed for item card display
 * Removes all logic from Blade templates
 */
class ItemCardViewModel
{
    public function __construct(
        public readonly int $itemId,
        public readonly string $variant,
        public readonly string $url,
        public readonly string $title,
        public readonly ?string $primaryImagePath,
        public readonly Collection $images,
        public readonly bool $hasMultipleImages,
        public readonly ?float $price,
        public readonly ?float $displayPrice,
        public readonly string $operationType,
        public readonly ?string $condition,
        public readonly ?string $category,
        public readonly ?string $createdAt,
        public readonly bool $showMeta,
        public readonly bool $showImagePreview,
        public readonly string $formattedPrice,
        public readonly string $formattedDisplayPrice,
        public readonly bool $isFree,
        public readonly bool $isRent,
        public readonly bool $showPriceUnit,
        public readonly string $operationTypeLabel,
        public readonly string $operationTypeBadgeClass,
        public readonly ?string $conditionLabel,
        public readonly array $previewImages,
    ) {}

    /**
     * Create from item data
     */
    public static function fromArray(array $data): self
    {
        $images = $data['images'] ?? collect();
        $hasMultipleImages = $images->count() > 1;
        $displayPrice = $data['displayPrice'] ?? null;
        $operationType = $data['operationType'] ?? 'sell';
        $isFree = $operationType === 'donate' || $displayPrice === null;
        $isRent = $operationType === 'rent';

        // Ensure price is float or null
        $price = null;
        if (isset($data['price']) && $data['price'] !== null && $data['price'] !== '') {
            $price = is_numeric($data['price']) ? (float) $data['price'] : null;
        }

        // Ensure displayPrice is float or null
        if ($displayPrice !== null && !is_float($displayPrice)) {
            $displayPrice = is_numeric($displayPrice) ? (float) $displayPrice : null;
        }

        return new self(
            itemId: $data['itemId'] ?? ($data['item']->id ?? uniqid()),
            variant: $data['variant'] ?? 'public',
            url: $data['url'] ?? '#',
            title: $data['title'] ?? '',
            primaryImagePath: $data['primaryImagePath'] ?? null,
            images: $images,
            hasMultipleImages: $hasMultipleImages,
            price: $price,
            displayPrice: $displayPrice,
            operationType: $operationType,
            condition: $data['condition'] ?? null,
            category: $data['category'] ?? null,
            createdAt: $data['createdAt'] ?? null,
            showMeta: $data['showMeta'] ?? true,
            showImagePreview: $data['showImagePreview'] ?? true,
            formattedPrice: $price ? number_format($price, 0) : '',
            formattedDisplayPrice: $displayPrice ? number_format($displayPrice, 0) : '',
            isFree: $isFree,
            isRent: $isRent,
            showPriceUnit: $isRent && $displayPrice !== null,
            operationTypeLabel: __('items.operation_types.' . $operationType),
            operationTypeBadgeClass: 'khezana-item-card__badge--' . $operationType,
            conditionLabel: isset($data['condition']) && $data['condition'] 
                ? __('items.conditions.' . $data['condition']) 
                : null,
            previewImages: $hasMultipleImages && $data['showImagePreview'] !== false
                ? $images->take(4)->map(fn($img) => [
                    'path' => $img->path ?? null,
                    'url' => $img->path ? asset('storage/' . $img->path) : null,
                ])->filter(fn($img) => $img['path'] !== null)->toArray()
                : [],
        );
    }

    /**
     * Convert to array for Blade
     */
    public function toArray(): array
    {
        return [
            'itemId' => $this->itemId,
            'variant' => $this->variant,
            'url' => $this->url,
            'title' => $this->title,
            'primaryImagePath' => $this->primaryImagePath,
            'images' => $this->images,
            'hasMultipleImages' => $this->hasMultipleImages,
            'hasImage' => $this->hasImage(),
            'price' => $this->price,
            'displayPrice' => $this->displayPrice,
            'hasPrice' => $this->hasPrice(),
            'operationType' => $this->operationType,
            'condition' => $this->condition,
            'category' => $this->category,
            'createdAt' => $this->createdAt,
            'showMeta' => $this->showMeta,
            'showImagePreview' => $this->showImagePreview,
            'formattedPrice' => $this->formattedPrice,
            'formattedDisplayPrice' => $this->formattedDisplayPrice,
            'isFree' => $this->isFree,
            'isRent' => $this->isRent,
            'showPriceUnit' => $this->showPriceUnit,
            'operationTypeLabel' => $this->operationTypeLabel,
            'operationTypeBadgeClass' => $this->operationTypeBadgeClass,
            'conditionLabel' => $this->conditionLabel,
            'previewImages' => $this->previewImages,
            'getImageUrl' => $this->getImageUrl(),
            'getAdditionalImagesCount' => $this->getAdditionalImagesCount(),
        ];
    }

    /**
     * Check if should show image placeholder
     */
    public function hasImage(): bool
    {
        return $this->primaryImagePath !== null;
    }

    /**
     * Get image URL (computed property for Blade)
     */
    public function getImageUrl(): ?string
    {
        // primaryImagePath now contains the full URL from Storage
        return $this->primaryImagePath;
    }

    /**
     * Get price display text
     */
    public function getPriceDisplay(): string
    {
        if ($this->isFree) {
            return __('common.ui.free');
        }

        if ($this->displayPrice !== null) {
            return $this->formattedDisplayPrice . ' ' . __('common.ui.currency');
        }

        return '';
    }

    /**
     * Get additional images count text
     */
    public function getAdditionalImagesCount(): string
    {
        return $this->hasMultipleImages ? '+' . ($this->images->count() - 1) : '';
    }

    /**
     * Check if has price to display
     */
    public function hasPrice(): bool
    {
        return $this->displayPrice !== null && !$this->isFree;
    }
}
