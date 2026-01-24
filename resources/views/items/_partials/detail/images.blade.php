{{-- Item Images Partial – Phase 3.3: WebP + srcset --}}
{{-- Usage: @include('items._partials.detail.images', ['viewModel' => $viewModel]) --}}

<div class="khezana-item-images">
    @if ($viewModel->hasImages && !empty($viewModel->imageUrls))
        {{-- Main Image (plain img for changeMainImage JS) --}}
        <div class="khezana-item-main-image">
            @if (!empty($viewModel->imageUrls[0]['url']))
                @php $img = $viewModel->imageUrls[0]; @endphp
                <img id="mainImage" src="{{ $img['url'] }}" alt="{{ $viewModel->title }}"
                    class="khezana-main-img" loading="eager" decoding="async"
                    srcset="{{ $img['url'] }} 1920w" sizes="(max-width: 768px) 100vw, 50vw"
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
                            <x-responsive-image
                                :url="$image['url']"
                                :urlWebp="$image['url_webp'] ?? null"
                                :alt="$viewModel->title"
                                loading="lazy"
                                decoding="async"
                            />
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
