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
            'primaryImagePath' => $item->primaryImage ? self::getImageUrl($item->primaryImage) : null,
            'images' => $item->images ?? collect(),
            'title' => $item->title ?? '',
            'price' => $item->price ? (is_numeric($item->price) ? (float) $item->price : null) : null,
            'displayPrice' => $item->price 
                ? (is_numeric($item->price) ? (float) price_with_fee((float) $item->price, $item->operationType ?? 'sell') : null)
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

        // Get approval status
        $approvalStatus = $item->approvalRelation?->status;
        $approvalStatusValue = $approvalStatus?->value ?? null;
        $approvalStatusLabel = $approvalStatus?->label() ?? null;
        $approvalStatusColor = $approvalStatus?->color() ?? null;

        $data = [
            'itemId' => $item->id,
            'variant' => $overrides['variant'] ?? 'user',
            'url' => route('items.show', $item),
            'primaryImagePath' => $primaryImage ? self::getImageUrl($primaryImage) : null,
            'images' => $item->images ?? collect(),
            'title' => $item->title ?? '',
            'price' => $item->price ? (is_numeric($item->price) ? (float) $item->price : null) : null,
            'displayPrice' => $item->price 
                ? (is_numeric($item->price) ? (float) price_with_fee((float) $item->price, $item->operation_type->value ?? 'sell') : null)
                : null,
            'operationType' => $item->operation_type->value ?? 'sell',
            'condition' => $item->condition ?? null,
            'category' => $item->category?->name ?? null,
            'createdAt' => null, // User items don't show created date in card
            'showMeta' => $overrides['showMeta'] ?? true,
            'showImagePreview' => $overrides['showImagePreview'] ?? true,
            'approvalStatus' => $approvalStatusValue,
            'approvalStatusLabel' => $approvalStatusLabel,
            'approvalStatusColor' => $approvalStatusColor,
            'isVerificationRequired' => $approvalStatusValue === 'verification_required',
            'verificationMessage' => $item->approvalRelation?->rejection_reason ?? null,
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

    /**
     * Get image URL from ItemImage or ImageReadModel
     */
    private static function getImageUrl($image): ?string
    {
        if (!$image) {
            return null;
        }

        $path = $image->path ?? null;
        $disk = $image->disk ?? 'public';

        if (!$path) {
            return null;
        }

        $url = asset('storage/' . $path);

        // #region agent log H3
        $logPath = base_path('.cursor/debug.log');
        file_put_contents($logPath, json_encode([
            'location' => 'ItemCardHelper.php:getImageUrl',
            'message' => 'Image URL generated',
            'hypothesisId' => 'H3',
            'data' => [
                'path' => $path,
                'disk' => $disk,
                'url' => $url,
                'file_exists' => file_exists(storage_path('app/public/' . $path)),
            ],
            'timestamp' => round(microtime(true) * 1000),
            'sessionId' => 'debug-session',
        ]) . "\n", FILE_APPEND);
        // #endregion

        return $url;
    }
}
