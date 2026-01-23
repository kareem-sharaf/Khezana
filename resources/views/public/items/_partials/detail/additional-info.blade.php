{{-- Public Item Additional Info Partial --}}
{{-- Usage: @include('public.items._partials.detail.additional-info', ['viewModel' => $viewModel]) --}}

<div class="khezana-item-additional-info">
    <div class="khezana-info-item">
        <span class="khezana-info-icon">📅</span>
        <span class="khezana-info-text">{{ __('common.ui.published') }} {{ $viewModel->createdAtFormatted }}</span>
    </div>
    @if ($viewModel->userName ?? null)
        <div class="khezana-info-item">
            <span class="khezana-info-icon">👤</span>
            <span class="khezana-info-text">{{ __('common.ui.from') }} {{ $viewModel->userName }}</span>
        </div>
    @endif
</div>
