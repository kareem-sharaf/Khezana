{{-- Public Item Description Partial --}}
{{-- Usage: @include('public.items._partials.detail.description', ['viewModel' => $viewModel]) --}}

@if ($viewModel->hasDescription)
    <div class="khezana-item-description">
        <h3 class="khezana-section-title-small">{{ __('common.ui.description') }}</h3>
        <p class="khezana-description-text">{{ $viewModel->description }}</p>
    </div>
@endif
