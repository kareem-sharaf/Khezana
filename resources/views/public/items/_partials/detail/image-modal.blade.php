{{-- Image Modal Partial --}}
{{-- Usage: @include('public.items._partials.detail.image-modal', ['viewModel' => $viewModel]) --}}

<div class="khezana-image-modal" id="imageModal" role="dialog" aria-modal="true" aria-label="{{ __('common.ui.image_gallery') }}" style="display: none;">
    <div class="khezana-image-modal-overlay" id="modalOverlay"></div>
    <div class="khezana-image-modal-content">
        <button type="button" class="khezana-image-modal-close" id="closeModal" aria-label="{{ __('common.ui.close') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        <button type="button" class="khezana-image-modal-nav khezana-image-modal-prev" id="modalPrev" aria-label="{{ __('common.ui.previous_image') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </button>
        <div class="khezana-image-modal-image-container">
            <img id="modalImage" src="" alt="" class="khezana-image-modal-img">
            <div class="khezana-image-modal-loader" id="modalLoader">
                <div class="khezana-modal-skeleton"></div>
            </div>
        </div>
        <button type="button" class="khezana-image-modal-nav khezana-image-modal-next" id="modalNext" aria-label="{{ __('common.ui.next_image') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </button>
        <div class="khezana-image-modal-counter">
            <span id="modalImageIndex">1</span> / <span id="modalTotalImages">{{ count($viewModel->imageUrls) }}</span>
        </div>
    </div>
</div>
