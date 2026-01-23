<?php

declare(strict_types=1);

namespace App\Helpers;

use App\ViewModels\Items\ItemCardViewModel;

/**
 * Item Card Helper
 * 
 * Static helper methods to prepare item card data
 * Now uses ViewModels for clean data preparation
 */
class ItemCardHelper
{
    /**
     * Prepare item card ViewModel for public items (ItemReadModel)
     */
    public static function preparePublicItem($item, array $overrides = []): array
    {
        $data = [
            'itemId' => $item->id,
            'variant' => $overrides['variant'] ?? 'public',
            'url' => route('public.items.show', [
                'id' => $item->id,
                'slug' => $item->slug ?? null
            ]),
            'primaryImagePath' => $item->primaryImage->path ?? null,
            'images' => $item->images ?? collect(),
            'title' => $item->title ?? '',
            'price' => $item->price ?? null,
            'displayPrice' => $item->price 
                ? price_with_fee((float) $item->price, $item->operationType ?? 'sell')
                : null,
            'operationType' => $item->operationType ?? 'sell',
            'condition' => $item->condition ?? null,
            'category' => $item->category?->name ?? null,
            'createdAt' => $item->createdAtFormatted ?? null,
            'showMeta' => $overrides['showMeta'] ?? true,
            'showImagePreview' => $overrides['showImagePreview'] ?? true,
        ];

        $viewModel = ItemCardViewModel::fromArray(array_merge($data, $overrides));
        return array_merge(['item' => $item], $viewModel->toArray());
    }

    /**
     * Prepare item card ViewModel for user items (Item model)
     */
    public static function prepareUserItem($item, array $overrides = []): array
    {
        $primaryImage = $item->images && $item->images->count() > 0
            ? ($item->images->where('is_primary', true)->first() ?? $item->images->first())
            : null;

        $data = [
            'itemId' => $item->id,
            'variant' => $overrides['variant'] ?? 'user',
            'url' => route('items.show', $item),
            'primaryImagePath' => $primaryImage->path ?? null,
            'images' => $item->images ?? collect(),
            'title' => $item->title ?? '',
            'price' => $item->price ?? null,
            'displayPrice' => $item->price 
                ? price_with_fee((float) $item->price, $item->operation_type->value ?? 'sell')
                : null,
            'operationType' => $item->operation_type->value ?? 'sell',
            'condition' => $item->condition ?? null,
            'category' => $item->category?->name ?? null,
            'createdAt' => null, // User items don't show created date in card
            'showMeta' => $overrides['showMeta'] ?? true,
            'showImagePreview' => $overrides['showImagePreview'] ?? true,
        ];

        $viewModel = ItemCardViewModel::fromArray(array_merge($data, $overrides));
        return array_merge(['item' => $item], $viewModel->toArray());
    }

    /**
     * Prepare item card ViewModel for compact variant
     */
    public static function prepareCompactItem($item, string $type = 'public', array $overrides = []): array
    {
        $baseData = $type === 'public'
            ? self::preparePublicItem($item)
            : self::prepareUserItem($item);

        return array_merge($baseData, [
            'variant' => 'compact',
            'showMeta' => false,
            'showImagePreview' => false,
        ], $overrides);
    }
}
