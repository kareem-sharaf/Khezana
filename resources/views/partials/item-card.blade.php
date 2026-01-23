{{--
    Item Card Component - Clean UI Component (Dumb Component)
    Uses ViewModel for all data preparation - No logic in Blade
    
    All data is prepared in ItemCardViewModel via ItemCardHelper
    This template only displays pre-calculated values
--}}

<article 
    class="khezana-item-card khezana-item-card--{{ $variant }}" 
    data-item-id="{{ $itemId }}"
    data-variant="{{ $variant }}">
    
    {{-- Image Section --}}
    <div class="khezana-item-card__image">
        @if ($hasImage)
            <a href="{{ $url }}" class="khezana-item-card__image-link" aria-label="{{ $title }}">
                <div class="khezana-item-card__image-container">
                    <img 
                        src="{{ $getImageUrl }}"
                        alt="{{ $title }}"
                        class="khezana-item-card__image-element"
                        loading="lazy"
                        data-primary-image="{{ $getImageUrl }}"
                        onload="this.classList.add('loaded'); const skeleton = document.getElementById('skeleton-{{ $itemId }}'); if(skeleton) { skeleton.style.display = 'none'; }"
                        onerror="this.classList.add('loaded'); const skeleton = document.getElementById('skeleton-{{ $itemId }}'); if(skeleton) { skeleton.style.display = 'none'; }">
                    
                    @if ($hasMultipleImages && $variant !== 'compact')
                        <div class="khezana-item-card__image-indicator">
                            <span class="khezana-item-card__image-count">{{ $getAdditionalImagesCount }}</span>
                        </div>
                        
                        @if ($showImagePreview && !empty($previewImages))
                            <div class="khezana-item-card__image-preview" data-images-count="{{ count($previewImages) }}">
                                @foreach ($previewImages as $previewImage)
                                    <img 
                                        src="{{ $previewImage['url'] }}"
                                        alt="{{ $title }}"
                                        class="khezana-item-card__preview-image"
                                        data-image-index="{{ $loop->index }}"
                                        loading="lazy">
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
            @if ($hasPrice)
                <div class="khezana-item-card__price">
                    {{ $formattedDisplayPrice }} {{ __('common.ui.currency') }}
                    @if ($showPriceUnit)
                        <span class="khezana-item-card__price-unit">{{ __('common.ui.per_day') }}</span>
                    @endif
                </div>
            @elseif ($isFree)
                <div class="khezana-item-card__price khezana-item-card__price--free">
                    {{ __('common.ui.free') }}
                </div>
            @endif
            
            {{-- Operation Type Badge --}}
            <span 
                class="khezana-item-card__badge {{ $operationTypeBadgeClass }}" 
                aria-label="{{ $operationTypeLabel }}">
                {{ $operationTypeLabel }}
            </span>
        </div>

        {{-- Secondary Meta Info --}}
        @if ($showMeta && $variant !== 'compact')
            <div class="khezana-item-card__meta">
                @if ($conditionLabel)
                    <span class="khezana-item-card__meta-item" aria-label="{{ __('items.fields.condition') }}">
                        🏷️ {{ $conditionLabel }}
                    </span>
                @endif
                
                @if ($category)
                    <span class="khezana-item-card__meta-item" aria-label="{{ __('items.fields.category') }}">
                        {{ $category }}
                    </span>
                @endif
                
                @if ($createdAt)
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
