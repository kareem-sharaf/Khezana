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
        $operationType = $type === 'public' 
            ? ($item->operationType ?? 'sell')
            : ($item->operation_type->value ?? 'sell');
        
        $price = $type === 'public' 
            ? ($item->price ?? null)
            : ($item->price ?? null);
        
        $displayPrice = $price ? price_with_fee((float) $price, $operationType) : null;
        $isFree = $operationType === 'donate' || $displayPrice === null;
        $isRent = $operationType === 'rent';
        
        $images = $type === 'public'
            ? ($item->images ?? collect())
            : ($item->images ?? collect());
        
        $primaryImage = $type === 'public'
            ? ($item->primaryImage ?? null)
            : ($images->where('is_primary', true)->first() ?? $images->first());
        
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
        
        $breadcrumbs = $type === 'public'
            ? [
                ['label' => __('common.ui.home'), 'url' => route('home')],
                ['label' => __('common.ui.items_page'), 'url' => route('public.items.index')],
                ['label' => $item->title, 'url' => null],
            ]
            : [
                ['label' => __('common.ui.home'), 'url' => route('home')],
                ['label' => __('common.ui.my_items_page'), 'url' => route('items.index')],
                ['label' => $item->title, 'url' => null],
            ];
        
        // Check permissions
        $isPending = $type === 'user' && method_exists($item, 'isPending') ? $item->isPending() : ($approvalStatus === 'pending');
        $isApproved = $type === 'user' && method_exists($item, 'isApproved') ? $item->isApproved() : ($approvalStatus === 'approved');
        
        $canEdit = $type === 'user' && !$isPending;
        $canDelete = $type === 'user';
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
            depositAmount: $type === 'public' 
                ? ($item->depositAmount ?? null)
                : ($item->deposit_amount ?? null),
            isFree: $isFree,
            isRent: $isRent,
            hasPrice: $displayPrice !== null,
            hasDeposit: ($type === 'public' ? ($item->depositAmount ?? null) : ($item->deposit_amount ?? null)) !== null,
            formattedPrice: $price ? number_format((float) $price, 0) : '',
            formattedDisplayPrice: $displayPrice ? number_format($displayPrice, 0) : '',
            formattedDeposit: ($type === 'public' ? ($item->depositAmount ?? null) : ($item->deposit_amount ?? null))
                ? number_format((float) ($type === 'public' ? $item->depositAmount : $item->deposit_amount), 0)
                : '',
            showPriceUnit: $isRent && $displayPrice !== null,
            images: $images,
            primaryImage: $primaryImage,
            hasImages: $images->count() > 0,
            hasMultipleImages: $images->count() > 1,
            imageUrls: $images->map(fn($img) => [
                'path' => $img->path ?? null,
                'url' => $img->path ? asset('storage/' . $img->path) : null,
                'isPrimary' => $img->is_primary ?? false,
            ])->toArray(),
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
            attributes: $type === 'public'
                ? ($item->attributes ?? collect())
                : ($item->itemAttributes ?? collect()),
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
            userName: $type === 'public' ? ($item->user?->name ?? null) : null,
        );
    }

    /**
     * Convert to array for Blade
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
