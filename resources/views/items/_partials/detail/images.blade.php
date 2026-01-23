{{-- Item Images Partial --}}
{{-- Usage: @include('items._partials.detail.images', ['viewModel' => $viewModel]) --}}

<div class="khezana-item-images">
    @if ($viewModel->hasImages && !empty($viewModel->imageUrls))
        {{-- Main Image --}}
        <div class="khezana-item-main-image">
            @if (!empty($viewModel->imageUrls[0]['url']))
                <img id="mainImage" src="{{ $viewModel->imageUrls[0]['url'] }}" alt="{{ $viewModel->title }}"
                    class="khezana-main-img" loading="eager" decoding="async"
                    onerror="this.onerror=null; this.style.display='none';">
            @else
                <div class="khezana-no-image-placeholder">
                    {{ __('common.ui.no_image') }}
                </div>
            @endif
        </div>

        {{-- Thumbnails --}}
        @if ($viewModel->hasMultipleImages && count($viewModel->imageUrls) > 1)
            <div class="khezana-item-thumbnails">
                @foreach ($viewModel->imageUrls as $image)
                    @if (!empty($image['url']))
                        <button type="button" class="khezana-thumbnail {{ $loop->first ? 'active' : '' }}"
                            onclick="changeMainImage('{{ $image['url'] }}', this)">
                            <img src="{{ $image['url'] }}" alt="{{ $viewModel->title }}" loading="lazy" decoding="async"
                                onerror="this.onerror=null; this.parentElement.style.display='none';">
                        </button>
                    @endif
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
