{{--
    Item Card Component - Clean UI Component (Dumb Component)
    
    Props:
    - $item: Item model or ItemReadModel
    - $variant: 'public' | 'user' | 'compact' (default: 'public')
    - $url: Item detail URL (required)
    - $primaryImage: Primary image object with 'path' property
    - $images: Collection of images (optional, for multi-image indicator)
    - $title: Item title (required)
    - $price: Item price (float|null)
    - $displayPrice: Final price with fees (float|null)
    - $operationType: 'sell' | 'rent' | 'donate' (required)
    - $condition: 'new' | 'used' (optional)
    - $category: Category name (optional)
    - $createdAt: Formatted created date (optional)
    - $showMeta: Show secondary meta info (default: true)
    - $showImagePreview: Show hover preview (default: true)
    
    Usage:
    @include('partials.item-card', [
        'item' => $item,
        'variant' => 'public',
        'url' => route('public.items.show', ['id' => $item->id]),
        'primaryImage' => $item->primaryImage,
        'images' => $item->images,
        'title' => $item->title,
        'price' => $item->price,
        'displayPrice' => price_with_fee($item->price, $item->operationType),
        'operationType' => $item->operationType,
        'condition' => $item->condition,
        'category' => $item->category?->name,
        'createdAt' => $item->createdAtFormatted,
    ])
--}}

@php
    // Default values
    $variant = $variant ?? 'public';
    $showMeta = $showMeta ?? true;
    $showImagePreview = $showImagePreview ?? true;
    $images = $images ?? collect();
    $hasMultipleImages = $images->count() > 1;
    $primaryImagePath = $primaryImage->path ?? null;
    $itemId = $item->id ?? $item['id'] ?? uniqid();
@endphp

<article 
    class="khezana-item-card khezana-item-card--{{ $variant }}" 
    data-item-id="{{ $itemId }}"
    data-variant="{{ $variant }}">
    
    {{-- Image Section --}}
    <div class="khezana-item-card__image">
        @if ($primaryImagePath)
            <a href="{{ $url }}" class="khezana-item-card__image-link" aria-label="{{ $title }}">
                <div class="khezana-item-card__image-container">
                    <img 
                        src="{{ asset('storage/' . $primaryImagePath) }}"
                        alt="{{ $title }}"
                        class="khezana-item-card__image-element"
                        loading="lazy"
                        data-primary-image="{{ asset('storage/' . $primaryImagePath) }}"
                        onload="this.classList.add('loaded'); const skeleton = document.getElementById('skeleton-{{ $itemId }}'); if(skeleton) { skeleton.style.display = 'none'; }"
                        onerror="this.classList.add('loaded'); const skeleton = document.getElementById('skeleton-{{ $itemId }}'); if(skeleton) { skeleton.style.display = 'none'; }">
                    
                    @if ($hasMultipleImages && $variant !== 'compact')
                        <div class="khezana-item-card__image-indicator">
                            <span class="khezana-item-card__image-count">+{{ $images->count() - 1 }}</span>
                        </div>
                        
                        @if ($showImagePreview)
                            <div class="khezana-item-card__image-preview" data-images-count="{{ $images->count() }}">
                                @foreach ($images->take(4) as $image)
                                    @if($image->path ?? null)
                                        <img 
                                            src="{{ asset('storage/' . $image->path) }}"
                                            alt="{{ $title }}"
                                            class="khezana-item-card__preview-image"
                                            data-image-index="{{ $loop->index }}"
                                            loading="lazy">
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endif
                    
                    {{-- Skeleton Loader --}}
                    <div class="khezana-item-card__image-skeleton" id="skeleton-{{ $itemId }}" aria-hidden="true">
                        <div class="khezana-skeleton-shimmer"></div>
                    </div>
                </div>
            </a>
        @else
            <a href="{{ $url }}" class="khezana-item-card__image-link" aria-label="{{ $title }}">
                <div class="khezana-item-card__image-placeholder">
                    <svg class="khezana-item-card__placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    <span class="khezana-item-card__placeholder-text">{{ __('common.ui.no_image') }}</span>
                </div>
            </a>
        @endif
    </div>

    {{-- Content Section --}}
    <div class="khezana-item-card__content">
        {{-- Title --}}
        <h3 class="khezana-item-card__title">
            <a href="{{ $url }}" class="khezana-item-card__title-link">
                {{ $title }}
            </a>
        </h3>

        {{-- Price & Badge Section --}}
        <div class="khezana-item-card__price-section">
            @if (isset($displayPrice) && $displayPrice !== null)
                <div class="khezana-item-card__price">
                    {{ number_format($displayPrice, 0) }} {{ __('common.ui.currency') }}
                    @if ($operationType === 'rent')
                        <span class="khezana-item-card__price-unit">{{ __('common.ui.per_day') }}</span>
                    @endif
                </div>
            @elseif ($operationType === 'donate')
                <div class="khezana-item-card__price khezana-item-card__price--free">
                    {{ __('common.ui.free') }}
                </div>
            @endif
            
            {{-- Operation Type Badge --}}
            <span 
                class="khezana-item-card__badge khezana-item-card__badge--{{ $operationType }}" 
                aria-label="{{ __('items.operation_types.' . $operationType) }}">
                {{ __('items.operation_types.' . $operationType) }}
            </span>
        </div>

        {{-- Secondary Meta Info --}}
        @if ($showMeta && $variant !== 'compact')
            <div class="khezana-item-card__meta">
                @if (isset($condition) && $condition)
                    <span class="khezana-item-card__meta-item" aria-label="{{ __('items.fields.condition') }}">
                        🏷️ {{ __('items.conditions.' . $condition) }}
                    </span>
                @endif
                
                @if (isset($category) && $category)
                    <span class="khezana-item-card__meta-item" aria-label="{{ __('items.fields.category') }}">
                        {{ $category }}
                    </span>
                @endif
                
                @if (isset($createdAt) && $createdAt)
                    <span class="khezana-item-card__meta-item" aria-label="{{ __('common.ui.posted') }}">
                        {{ __('common.ui.posted') }} {{ $createdAt }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- Actions Section (Reserved for future features) --}}
    @if ($variant === 'user')
        <div class="khezana-item-card__actions" aria-label="{{ __('common.ui.item_actions') }}">
            {{-- Reserved for future: quick actions, edit, delete --}}
        </div>
    @endif
</article>
