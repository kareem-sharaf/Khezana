<?php

declare(strict_types=1);

namespace App\ViewModels\Items;

use Illuminate\Support\Collection;

/**
 * Item Detail ViewModel
 *
 * Prepares all data needed for item detail page
 * Removes all logic from Blade templates
 */
class ItemDetailViewModel
{
    public function __construct(
        public readonly int $itemId,
        public readonly string $title,
        public readonly string $url,
        public readonly string $operationType,
        public readonly string $operationTypeLabel,
        public readonly string $operationTypeBadgeClass,
        public readonly ?float $price,
        public readonly ?float $displayPrice,
        public readonly ?float $depositAmount,
        public readonly bool $isFree,
        public readonly bool $isRent,
        public readonly bool $hasPrice,
        public readonly bool $hasDeposit,
        public readonly string $formattedPrice,
        public readonly string $formattedDisplayPrice,
        public readonly string $formattedDeposit,
        public readonly bool $showPriceUnit,
        public readonly Collection $images,
        public readonly ?object $primaryImage,
        public readonly bool $hasImages,
        public readonly bool $hasMultipleImages,
        public readonly array $imageUrls,
        public readonly ?string $category,
        public readonly ?string $condition,
        public readonly ?string $conditionLabel,
        public readonly bool $isAvailable,
        public readonly string $availabilityLabel,
        public readonly Collection $attributes,
        public readonly bool $hasAttributes,
        public readonly ?string $description,
        public readonly bool $hasDescription,
        public readonly ?object $approvalRelation,
        public readonly ?string $approvalStatus,
        public readonly ?string $approvalStatusClass,
        public readonly ?string $approvalStatusLabel,
        public readonly bool $isPending,
        public readonly bool $isApproved,
        public readonly bool $isRejected,
        public readonly ?string $rejectionReason,
        public readonly string $createdAtFormatted,
        public readonly string $updatedAtFormatted,
        public readonly bool $showUpdatedAt,
        public readonly array $breadcrumbs,
        public readonly bool $canEdit,
        public readonly bool $canDelete,
        public readonly bool $canSubmitForApproval,
        public readonly string $editUrl,
        public readonly string $deleteUrl,
        public readonly string $submitForApprovalUrl,
        public readonly ?string $userName = null,
    ) {}

    /**
     * Create from item model
     */
    public static function fromItem($item, string $type = 'user'): self
    {
        // Get user name for public items
        $userName = null;
        if ($type === 'public') {
            if ($item instanceof \App\Read\Items\Models\ItemReadModel) {
                $userName = $item->user?->name ?? null;
            } elseif (isset($item->user)) {
                if ($item->user instanceof \App\Read\Shared\Models\UserReadModel) {
                    $userName = $item->user->name;
                } elseif (is_object($item->user) && isset($item->user->name)) {
                    $userName = $item->user->name;
                }
            }
        }

        $operationType = $type === 'public'
            ? ($item->operationType ?? 'sell')
            : ($item->operation_type->value ?? 'sell');

        $price = $type === 'public'
            ? ($item->price ?? null)
            : ($item->price ?? null);

        // Convert price to float if it's not null
        $price = $price !== null ? (float) $price : null;

        $displayPrice = $price ? price_with_fee($price, $operationType) : null;
        $isFree = $operationType === 'donate' || $displayPrice === null;
        $isRent = $operationType === 'rent';

        $images = $type === 'public'
            ? ($item->images ?? collect())
            : ($item->images ?? collect());

        // Get primary image - handle both ItemReadModel and Item model
        $primaryImage = null;
        if ($type === 'public') {
            // For ItemReadModel, primaryImage is ImageReadModel
            if ($item instanceof \App\Read\Items\Models\ItemReadModel) {
                $primaryImage = $item->primaryImage;
            } elseif (isset($item->primaryImage)) {
                $primaryImage = $item->primaryImage;
            } else {
                // Fallback: get first image from images collection
                $primaryImage = $images->first();
            }
        } else {
            // For Item model, get primary from images collection
            $primaryImage = $images->where('is_primary', true)->first() ?? $images->first();
        }

        $approvalRelation = $type === 'user' ? ($item->approvalRelation ?? null) : null;
        $approvalStatus = $approvalRelation?->status?->value ?? null;

        $statusClass = match ($approvalStatus) {
            'approved' => 'khezana-approval-badge-approved',
            'pending' => 'khezana-approval-badge-pending',
            'rejected' => 'khezana-approval-badge-rejected',
            'archived' => 'khezana-approval-badge-archived',
            default => null,
        };

        $url = $type === 'public'
            ? route('public.items.show', ['id' => $item->id, 'slug' => $item->slug ?? null])
            : route('items.show', $item);

        // Note: breadcrumb component automatically adds "الرئيسية" (home), so we don't include it here
        $breadcrumbs = $type === 'public'
            ? [
                ['label' => __('common.ui.items_page'), 'url' => route('public.items.index')],
                ['label' => $item->title, 'url' => null],
            ]
            : [
                ['label' => __('common.ui.my_items_page'), 'url' => route('items.index')],
                ['label' => $item->title, 'url' => null],
            ];

        // Check permissions
        $isPending = $type === 'user' && method_exists($item, 'isPending') ? $item->isPending() : ($approvalStatus === 'pending');
        $isApproved = $type === 'user' && method_exists($item, 'isApproved') ? $item->isApproved() : ($approvalStatus === 'approved');

        // Can edit only if NOT approved and NOT pending (or if pending, can edit before approval)
        // Actually: can edit if pending or rejected, but NOT if approved
        $canEdit = $type === 'user' && !$isApproved;
        // Can delete only if NOT approved (regular users cannot delete approved items)
        $canDelete = $type === 'user' && !$isApproved;
        $canSubmitForApproval = $type === 'user' && !$isPending && !$isApproved;

        return new self(
            itemId: $item->id,
            title: $item->title ?? '',
            url: $url,
            operationType: $operationType,
            operationTypeLabel: __('items.operation_types.' . $operationType),
            operationTypeBadgeClass: 'khezana-item-badge-' . $operationType,
            price: $price,
            displayPrice: $displayPrice,
            depositAmount: ($type === 'public'
                ? ($item->depositAmount ?? null)
                : ($item->deposit_amount ?? null)) !== null
                ? (float) ($type === 'public' ? $item->depositAmount : $item->deposit_amount)
                : null,
            isFree: $isFree,
            isRent: $isRent,
            hasPrice: $displayPrice !== null,
            hasDeposit: ($type === 'public'
                ? ($item->depositAmount ?? null)
                : ($item->deposit_amount ?? null)) !== null,
            formattedPrice: $price !== null ? number_format($price, 0) : '',
            formattedDisplayPrice: $displayPrice !== null ? number_format($displayPrice, 0) : '',
            formattedDeposit: ($type === 'public' ? ($item->depositAmount ?? null) : ($item->deposit_amount ?? null)) !== null
                ? number_format((float) ($type === 'public' ? $item->depositAmount : $item->deposit_amount), 0)
                : '',
            showPriceUnit: $isRent && $displayPrice !== null,
            images: $images,
            primaryImage: $primaryImage,
            imageUrls: $images->map(function ($img) {
                $path = null;
                $pathWebp = null;
                $disk = 'public';
                $isPrimary = false;

                if ($img instanceof \App\Read\Shared\Models\ImageReadModel) {
                    $path = $img->path ?? null;
                    $pathWebp = $img->pathWebp ?? null;
                    $disk = $img->disk ?? 'public';
                    $isPrimary = $img->isPrimary ?? false;
                } elseif (is_object($img)) {
                    $path = $img->path ?? null;
                    $pathWebp = $img->path_webp ?? null;
                    $disk = $img->disk ?? 'public';
                    $isPrimary = $img->is_primary ?? false;
                } else {
                    return null;
                }

                if (!$path) {
                    return null;
                }

                $url = asset('storage/' . $path);
                $urlWebp = $pathWebp ? asset('storage/' . $pathWebp) : null;

                return [
                    'path' => $path,
                    'path_webp' => $pathWebp,
                    'disk' => $disk,
                    'url' => $url,
                    'url_webp' => $urlWebp,
                    'isPrimary' => $isPrimary,
                ];
            })->filter()->values()->toArray(),
            hasImages: $images->count() > 0,
            hasMultipleImages: $images->count() > 1,
            category: $type === 'public'
                ? ($item->category?->name ?? null)
                : ($item->category?->name ?? null),
            condition: $item->condition ?? null,
            conditionLabel: isset($item->condition) && $item->condition
                ? __('items.conditions.' . $item->condition)
                : null,
            isAvailable: $type === 'public' ? true : ($item->is_available ?? true),
            availabilityLabel: $type === 'public'
                ? __('common.ui.available')
                : (($item->is_available ?? true) ? __('common.ui.available') : __('common.ui.unavailable')),
            attributes: self::prepareAttributes(
                $type === 'public'
                    ? ($item->attributes ?? collect())
                    : ($item->itemAttributes ?? collect())
            ),
            hasAttributes: $type === 'public'
                ? ($item->attributes?->count() ?? 0) > 0
                : ($item->itemAttributes?->count() ?? 0) > 0,
            description: $item->description ?? null,
            hasDescription: !empty($item->description),
            approvalRelation: $approvalRelation,
            approvalStatus: $approvalStatus,
            approvalStatusClass: $statusClass,
            approvalStatusLabel: $approvalRelation?->status?->label() ?? null,
            isPending: $isPending,
            isApproved: $isApproved,
            isRejected: $approvalStatus === 'rejected',
            rejectionReason: $approvalRelation?->rejection_reason ?? null,
            createdAtFormatted: $type === 'public'
                ? ($item->createdAtFormatted ?? '')
                : ($item->created_at->diffForHumans() ?? ''),
            updatedAtFormatted: $type === 'public'
                ? ''
                : ($item->updated_at->diffForHumans() ?? ''),
            showUpdatedAt: $type === 'user' && $item->updated_at && $item->updated_at->ne($item->created_at),
            breadcrumbs: $breadcrumbs,
            canEdit: $canEdit,
            canDelete: $canDelete,
            canSubmitForApproval: $canSubmitForApproval,
            editUrl: $type === 'user' ? route('items.edit', $item) : '#',
            deleteUrl: $type === 'user' ? route('items.destroy', $item) : '#',
            submitForApprovalUrl: $type === 'user' ? route('items.submit-for-approval', $item) : '#',
            userName: $userName,
        );
    }

    /**
     * Prepare attributes with translated names
     */
    private static function prepareAttributes($attributes): Collection
    {
        if (!$attributes instanceof \Illuminate\Support\Collection) {
            $attributes = collect($attributes);
        }

        return $attributes->map(function ($attribute) {
            // Handle different attribute structures
            if (is_object($attribute)) {
                $name = $attribute->name ?? $attribute->attribute->name ?? '';

                // Use formattedValue if available (already processed by AttributeReadModel)
                // Otherwise use value and format it
                if (isset($attribute->formattedValue) && $attribute->formattedValue !== '') {
                    $value = $attribute->formattedValue;
                } else {
                    $value = $attribute->value ?? '';
                    // If it's an AttributeReadModel, formattedValue should already be set
                    // But if not, we need to format it here
                    if ($attribute instanceof \App\Read\Shared\Models\AttributeReadModel) {
                        $value = $attribute->formattedValue;
                    }
                }

                // Translate attribute name
                $translatedName = translate_attribute_name($name);

                // Create new object with translated name
                return (object) [
                    'name' => $translatedName,
                    'originalName' => $name,
                    'value' => $value,
                    'formattedValue' => $value,
                ];
            }

            return $attribute;
        });
    }

    /**
     * Convert to array for Blade
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
