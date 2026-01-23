{{-- Public Item Images Partial --}}
{{-- Usage: @include('public.items._partials.detail.images', ['viewModel' => $viewModel]) --}}

<div class="khezana-item-images" id="itemGallery">
    @if ($viewModel->hasImages)
        <div class="khezana-item-main-image" id="mainImageContainer">
            <button type="button" class="khezana-image-zoom-trigger" id="zoomTrigger" aria-label="{{ __('common.ui.zoom_image') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                    <line x1="11" y1="8" x2="11" y2="14"/>
                    <line x1="8" y1="11" x2="14" y2="11"/>
                </svg>
            </button>
            <img 
                id="mainImage"
                src="{{ $viewModel->imageUrls[0]['url'] ?? '' }}"
                alt="{{ $viewModel->title }}"
                class="khezana-main-img"
                loading="eager"
                data-image-index="0"
                data-full-image="{{ $viewModel->imageUrls[0]['url'] ?? '' }}">
            
            @if ($viewModel->hasMultipleImages)
                <div class="khezana-image-navigation">
                    <button type="button" class="khezana-image-nav-btn khezana-image-nav-prev" id="prevImage" aria-label="{{ __('common.ui.previous_image') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </button>
                    <button type="button" class="khezana-image-nav-btn khezana-image-nav-next" id="nextImage" aria-label="{{ __('common.ui.next_image') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>
                <div class="khezana-image-counter">
                    <span id="currentImageIndex">1</span> / <span id="totalImages">{{ count($viewModel->imageUrls) }}</span>
                </div>
            @endif
        </div>

        @if ($viewModel->hasMultipleImages)
            <div class="khezana-item-thumbnails" id="thumbnailsContainer">
                @foreach ($viewModel->imageUrls as $image)
                    <button 
                        type="button"
                        class="khezana-thumbnail {{ $loop->first ? 'active' : '' }}"
                        data-image-index="{{ $loop->index }}"
                        data-image-src="{{ $image['url'] }}"
                        aria-label="{{ __('common.ui.view_image') }} {{ $loop->iteration }}"
                        onclick="changeMainImage('{{ $image['url'] }}', {{ $loop->index }}, this)">
                        <img 
                            src="{{ $image['url'] }}"
                            alt="{{ $viewModel->title }} - {{ __('common.ui.image') }} {{ $loop->iteration }}"
                            loading="lazy">
                        <span class="khezana-thumbnail-overlay"></span>
                    </button>
                @endforeach
            </div>
        @endif
    @else
        <div class="khezana-item-main-image khezana-no-image">
            <div class="khezana-no-image-placeholder">
                <svg class="khezana-placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                <span class="khezana-placeholder-text">{{ __('common.ui.no_image') }}</span>
            </div>
        </div>
    @endif
</div>
