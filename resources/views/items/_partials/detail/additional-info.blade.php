{{-- Additional Info Partial --}}
{{-- Usage: @include('items._partials.detail.additional-info', ['viewModel' => $viewModel]) --}}

<div class="khezana-item-additional-info">
    <div class="khezana-info-item">
        <span class="khezana-info-icon">📅</span>
        <span class="khezana-info-text">{{ __('common.ui.published') }} {{ $viewModel->createdAtFormatted }}</span>
    </div>
    @if ($viewModel->showUpdatedAt)
        <div class="khezana-info-item">
            <span class="khezana-info-icon">🔄</span>
            <span class="khezana-info-text">{{ __('common.ui.last_updated') }} {{ $viewModel->updatedAtFormatted }}</span>
        </div>
    @endif
</div>
