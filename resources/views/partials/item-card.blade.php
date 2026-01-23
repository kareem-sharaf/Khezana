@php
    // Determine if this is a public item (ItemReadModel) or user item (Item model)
    $isPublicItem = isset($item->primaryImage) || (isset($item->operationType) && is_string($item->operationType));
    $itemUrl = $isPublicItem 
        ? route('public.items.show', ['id' => $item->id, 'slug' => $item->slug ?? $item->slug])
        : route('items.show', $item);
    
    $primaryImage = $isPublicItem ? $item->primaryImage : ($item->images && $item->images->count() > 0 ? ($item->images->where('is_primary', true)->first() ?? $item->images->first()) : null);
    $images = $isPublicItem ? ($item->images ?? collect()) : ($item->images ?? collect());
    $hasMultipleImages = $images && method_exists($images, 'count') && $images->count() > 1;
    
    $price = $isPublicItem ? $item->price : ($item->price ?? null);
    $operationType = $isPublicItem ? $item->operationType : $item->operation_type->value;
    $displayPrice = price_with_fee($price ? (float) $price : null, $operationType);
    $condition = $isPublicItem ? ($item->condition ?? null) : ($item->condition ?? null);
    $category = $isPublicItem ? ($item->category?->name ?? null) : ($item->category?->name ?? null);
    $createdAt = $isPublicItem ? ($item->createdAtFormatted ?? null) : null;
@endphp

<article class="khezana-item-card-modern" data-item-id="{{ $item->id }}">
    <!-- Image Section -->
    <div class="khezana-item-image-section">
        @if ($primaryImage && ($primaryImage->path ?? null))
            <a href="{{ $itemUrl }}" class="khezana-item-image-link" aria-label="{{ $item->title }}">
                <div class="khezana-item-image-container">
                    @php
                        $primaryImagePath = $primaryImage->path ?? '';
                    @endphp
                    <img 
                        src="{{ asset('storage/' . $primaryImagePath) }}"
                        alt="{{ $item->title }}"
                        class="khezana-item-image"
                        loading="lazy"
                        data-primary-image="{{ asset('storage/' . $primaryImagePath) }}"
                        onload="this.classList.add('loaded'); const skeleton = document.getElementById('skeleton-{{ $item->id }}'); if(skeleton) { skeleton.style.display = 'none'; skeleton.style.opacity = '0'; }"
                        onerror="this.classList.add('loaded'); const skeleton = document.getElementById('skeleton-{{ $item->id }}'); if(skeleton) { skeleton.style.display = 'none'; skeleton.style.opacity = '0'; }">
                    
                    @if ($hasMultipleImages)
                        <div class="khezana-item-image-indicator">
                            <span class="khezana-item-image-count">+{{ $images->count() - 1 }}</span>
                        </div>
                        
                        <!-- Hover Preview (Desktop Only) -->
                        <div class="khezana-item-image-preview" data-images-count="{{ $images->count() }}">
                            @foreach ($images->take(4) as $image)
                                @php
                                    $imagePath = $image->path ?? '';
                                @endphp
                                @if($imagePath)
                                <img 
                                    src="{{ asset('storage/' . $imagePath) }}"
                                    alt="{{ $item->title }}"
                                    class="khezana-preview-image"
                                    data-image-index="{{ $loop->index }}"
                                    loading="lazy">
                                @endif
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Skeleton Loader (hidden after image loads) -->
                    <div class="khezana-item-image-skeleton" id="skeleton-{{ $item->id }}" aria-hidden="true">
                        <div class="khezana-skeleton-shimmer"></div>
                    </div>
                </div>
            </a>
        @else
            <a href="{{ $itemUrl }}" class="khezana-item-image-link" aria-label="{{ $item->title }}">
                <div class="khezana-item-image-placeholder">
                    <svg class="khezana-placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    <span class="khezana-placeholder-text">{{ __('common.ui.no_image') }}</span>
                </div>
            </a>
        @endif
    </div>

    <!-- Content Section -->
    <div class="khezana-item-content-section">
        <!-- Title (Clickable) -->
        <h3 class="khezana-item-title-wrapper">
            <a href="{{ $itemUrl }}" class="khezana-item-title-link">
                {{ $item->title }}
            </a>
        </h3>

        <!-- Price (Priority) -->
        <div class="khezana-item-price-section">
            @if ($displayPrice !== null)
                <div class="khezana-item-price">
                    {{ number_format($displayPrice, 0) }} {{ __('common.ui.currency') }}
                    @if ($operationType == 'rent')
                        <span class="khezana-price-unit">{{ __('common.ui.per_day') }}</span>
                    @endif
                </div>
            @elseif ($operationType == 'donate')
                <div class="khezana-item-price khezana-item-price-free">
                    {{ __('common.ui.free') }}
                </div>
            @endif
            
            <!-- Operation Type Badge -->
            <span class="khezana-item-badge khezana-item-badge-{{ $operationType }}" aria-label="{{ __('items.operation_types.' . $operationType) }}">
                {{ __('items.operation_types.' . $operationType) }}
            </span>
        </div>

        <!-- Secondary Info (Subtle) -->
        <div class="khezana-item-secondary-info">
            @if ($condition)
                <span class="khezana-item-meta-text" aria-label="{{ __('items.fields.condition') }}">
                    🏷️ {{ __('items.conditions.' . $condition) }}
                </span>
            @endif
            
            @if ($category)
                <span class="khezana-item-meta-text" aria-label="{{ __('items.fields.category') }}">
                    {{ $category }}
                </span>
            @endif
            
            @if ($createdAt)
                <span class="khezana-item-meta-text" aria-label="{{ __('common.ui.posted') }}">
                    {{ __('common.ui.posted') }} {{ $createdAt }}
                </span>
            @endif
        </div>
    </div>

    <!-- Future Actions Placeholder (Hidden for now, ready for wishlist/share) -->
    <div class="khezana-item-actions" aria-label="{{ __('common.ui.item_actions') }}">
        <!-- Reserved for future: wishlist, share, compare buttons -->
    </div>
</article>
