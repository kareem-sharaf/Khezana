{{-- Item Images Partial --}}
{{-- Usage: @include('items._partials.detail.images', ['viewModel' => $viewModel]) --}}

<div class="khezana-item-images">
    @if ($viewModel->hasImages)
        {{-- Main Image --}}
        <div class="khezana-item-main-image">
            <img 
                id="mainImage" 
                src="{{ $viewModel->imageUrls[0]['url'] ?? '' }}" 
                alt="{{ $viewModel->title }}"
                class="khezana-main-img" 
                loading="eager">
        </div>

        {{-- Thumbnails --}}
        @if ($viewModel->hasMultipleImages)
            <div class="khezana-item-thumbnails">
                @foreach ($viewModel->imageUrls as $image)
                    <button 
                        type="button" 
                        class="khezana-thumbnail {{ $loop->first ? 'active' : '' }}"
                        onclick="changeMainImage('{{ $image['url'] }}', this)">
                        <img 
                            src="{{ $image['url'] }}" 
                            alt="{{ $viewModel->title }}"
                            loading="lazy">
                    </button>
                @endforeach
            </div>
        @endif
    @else
        <div class="khezana-item-main-image khezana-no-image">
            <div class="khezana-no-image-placeholder">
                {{ __('common.ui.no_image') }}
            </div>
        </div>
    @endif
</div>
