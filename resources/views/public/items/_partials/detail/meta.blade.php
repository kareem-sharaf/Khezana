{{-- Public Item Meta Information Partial --}}
{{-- Usage: @include('public.items._partials.detail.meta', ['viewModel' => $viewModel]) --}}

@if ($viewModel->category)
    <div class="khezana-item-meta">
        <span class="khezana-meta-label">{{ __('common.ui.category') }}:</span>
        <span class="khezana-meta-value">{{ $viewModel->category }}</span>
    </div>
@endif

@if ($viewModel->conditionLabel)
    <div class="khezana-item-meta">
        <span class="khezana-meta-label">🏷️ {{ __('items.fields.condition') }}:</span>
        <span class="khezana-meta-value">{{ $viewModel->conditionLabel }}</span>
    </div>
@endif
