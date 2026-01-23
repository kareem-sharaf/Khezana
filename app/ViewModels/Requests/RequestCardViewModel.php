<?php

declare(strict_types=1);

namespace App\ViewModels\Requests;

use Illuminate\Support\Collection;

/**
 * Request Card ViewModel
 * 
 * Prepares all data needed for request card display
 * Removes all logic from Blade templates
 */
class RequestCardViewModel
{
    public function __construct(
        public readonly int $requestId,
        public readonly string $variant,
        public readonly string $url,
        public readonly string $title,
        public readonly ?string $category,
        public readonly ?string $description,
        public readonly string $descriptionPreview,
        public readonly string $status,
        public readonly string $statusLabel,
        public readonly string $statusBadgeClass,
        public readonly ?string $approvalStatus,
        public readonly ?string $approvalStatusLabel,
        public readonly ?string $approvalStatusClass,
        public readonly bool $hasApprovalStatus,
        public readonly Collection $attributes,
        public readonly bool $hasAttributes,
        public readonly array $displayAttributes,
        public readonly string $createdAtFormatted,
        public readonly ?string $userName,
        public readonly bool $hasUser,
        public readonly int $offersCount,
        public readonly bool $hasOffers,
        public readonly string $offersText,
    ) {}

    /**
     * Create from request data
     */
    public static function fromRequest($request, string $type = 'public'): self
    {
        $status = $type === 'public'
            ? ($request->status ?? 'open')
            : ($request->status->value ?? 'open');
        
        $statusLabel = $type === 'public'
            ? ($request->statusLabel ?? __('requests.status.open'))
            : ($request->status->label() ?? __('requests.status.open'));
        
        $approvalRelation = $type === 'user' ? ($request->approvalRelation ?? null) : null;
        $approvalStatus = $approvalRelation?->status?->value ?? null;
        
        $approvalStatusClass = match ($approvalStatus) {
            'approved' => 'khezana-approval-badge-approved',
            'pending' => 'khezana-approval-badge-pending',
            'rejected' => 'khezana-approval-badge-rejected',
            'archived' => 'khezana-approval-badge-archived',
            default => null,
        };
        
        $attributes = $type === 'public'
            ? ($request->attributes ?? collect())
            : ($request->itemAttributes ?? collect());
        
        $displayAttributes = $attributes->take(3)->map(function ($attr) use ($type) {
            if ($type === 'public') {
                return [
                    'name' => $attr->name ?? '',
                    'value' => $attr->value ?? '',
                ];
            }
            return [
                'name' => $attr->attribute->name ?? '',
                'value' => $attr->value ?? '',
            ];
        })->toArray();
        
        $offersCount = $type === 'public'
            ? ($request->offersCount ?? 0)
            : ($request->offers->count() ?? 0);
        
        $url = $type === 'public'
            ? ($request->url ?? route('public.requests.show', ['id' => $request->id]))
            : route('requests.show', $request);
        
        $description = $request->description ?? null;
        $descriptionPreview = $description ? \Illuminate\Support\Str::limit($description, 120) : '';
        
        $createdAtFormatted = $type === 'public'
            ? ($request->createdAtFormatted ?? '')
            : ($request->created_at->diffForHumans() ?? '');
        
        return new self(
            requestId: $request->id,
            variant: $type,
            url: $url,
            title: $request->title ?? '',
            category: $type === 'public'
                ? ($request->category?->name ?? null)
                : ($request->category?->name ?? null),
            description: $description,
            descriptionPreview: $descriptionPreview,
            status: $status,
            statusLabel: $statusLabel,
            statusBadgeClass: 'khezana-request-badge-' . $status,
            approvalStatus: $approvalStatus,
            approvalStatusLabel: $approvalRelation?->status?->label() ?? null,
            approvalStatusClass: $approvalStatusClass,
            hasApprovalStatus: $approvalStatus !== null,
            attributes: $attributes,
            hasAttributes: $attributes->count() > 0,
            displayAttributes: $displayAttributes,
            createdAtFormatted: $createdAtFormatted,
            userName: $type === 'public' ? ($request->user?->name ?? null) : null,
            hasUser: $type === 'public' && isset($request->user),
            offersCount: $offersCount,
            hasOffers: $offersCount > 0,
            offersText: $offersCount . ' ' . __('common.ui.offers'),
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
