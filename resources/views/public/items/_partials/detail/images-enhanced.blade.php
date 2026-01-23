{{-- Enhanced Public Item Images Partial --}}
{{-- World-class image gallery with hover zoom, magnifier, and smooth transitions --}}
{{-- Usage: @include('public.items._partials.detail.images-enhanced', ['viewModel' => $viewModel]) --}}

<div class="khezana-image-gallery" id="itemGallery" data-gallery-id="gallery-{{ $viewModel->itemId }}">
    @if ($viewModel->hasImages && !empty($viewModel->imageUrls) && isset($viewModel->imageUrls[0]))
        {{-- Hero Viewport (Golden Ratio) --}}
        <div class="khezana-image-gallery__hero khezana-image-gallery__hero--zoom-enabled" 
             id="heroViewport"
             role="img"
             aria-label="{{ $viewModel->title }}">
            
            {{-- Loading Skeleton --}}
            <div class="khezana-image-gallery__loader" id="heroLoader">
                <div class="khezana-image-gallery__skeleton"></div>
            </div>
            
            {{-- Main Image Container --}}
            <div class="khezana-image-gallery__hero-container">
                <img 
                    id="mainImage"
                    src="{{ $viewModel->imageUrls[0]['url'] ?? '' }}"
                    alt="{{ $viewModel->title }}"
                    class="khezana-image-gallery__hero-img khezana-image-gallery__hero-img--fade-in"
                    loading="eager"
                    decoding="async"
                    data-image-index="0"
                    data-full-image="{{ $viewModel->imageUrls[0]['url'] ?? '' }}"
                    onload="this.closest('.khezana-image-gallery__hero').querySelector('.khezana-image-gallery__loader')?.classList.add('khezana-image-gallery__loader--hidden')"
                    onerror="this.onerror=null; this.style.display='none';">
            </div>
            
            {{-- Magnifier Lens (hidden by default) --}}
            <div class="khezana-image-gallery__magnifier" id="magnifierLens">
                <img class="khezana-image-gallery__magnifier-img" id="magnifierImg" src="" alt="">
            </div>
            
            {{-- Zoom Trigger Button --}}
            <button type="button" 
                    class="khezana-image-gallery__zoom-trigger" 
                    id="zoomTrigger" 
                    aria-label="{{ __('common.ui.zoom_image') }}"
                    title="{{ __('common.ui.zoom_image') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                    <line x1="11" y1="8" x2="11" y2="14"/>
                    <line x1="8" y1="11" x2="14" y2="11"/>
                </svg>
            </button>
            
            {{-- Navigation Arrows (if multiple images) --}}
            @if ($viewModel->hasMultipleImages)
                <div class="khezana-image-gallery__navigation">
                    <button type="button" 
                            class="khezana-image-gallery__nav-btn khezana-image-gallery__nav-btn--prev" 
                            id="prevImage" 
                            aria-label="{{ __('common.ui.previous_image') }}"
                            title="{{ __('common.ui.previous_image') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </button>
                    <button type="button" 
                            class="khezana-image-gallery__nav-btn khezana-image-gallery__nav-btn--next" 
                            id="nextImage" 
                            aria-label="{{ __('common.ui.next_image') }}"
                            title="{{ __('common.ui.next_image') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>
                
                {{-- Image Counter --}}
                <div class="khezana-image-gallery__counter">
                    <span id="currentImageIndex">1</span> / <span id="totalImages">{{ count($viewModel->imageUrls) }}</span>
                </div>
            @endif
        </div>

        {{-- Vertical Thumbnails Sidebar --}}
        @if ($viewModel->hasMultipleImages)
            <div class="khezana-image-gallery__thumbnails" id="thumbnailsContainer" role="list" aria-label="{{ __('common.ui.image_gallery') }}">
                @foreach ($viewModel->imageUrls as $image)
                    <button 
                        type="button"
                        class="khezana-image-gallery__thumbnail {{ $loop->first ? 'khezana-image-gallery__thumbnail--active' : '' }}"
                        data-image-index="{{ $loop->index }}"
                        data-image-src="{{ $image['url'] }}"
                        aria-label="{{ __('common.ui.view_image') }} {{ $loop->iteration }}"
                        role="listitem"
                        onclick="changeMainImage('{{ $image['url'] }}', {{ $loop->index }}, this)">
                        <img 
                            src="{{ $image['url'] }}"
                            alt="{{ $viewModel->title }} - {{ __('common.ui.image') }} {{ $loop->iteration }}"
                            loading="lazy"
                            decoding="async"
                            onerror="this.onerror=null; this.parentElement.style.display='none';">
                        <span class="khezana-image-gallery__thumbnail-preview"></span>
                        {{-- Future: Add icon for video/360 if needed --}}
                        {{-- <span class="khezana-image-gallery__thumbnail-icon">▶</span> --}}
                    </button>
                @endforeach
            </div>
        @endif
    @else
        {{-- No Image State --}}
        <div class="khezana-image-gallery__hero khezana-image-gallery__hero--no-image">
            <div class="khezana-image-gallery__placeholder">
                <svg class="khezana-image-gallery__placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                <span class="khezana-image-gallery__placeholder-text">{{ __('common.ui.no_image') }}</span>
            </div>
        </div>
    @endif
</div>
