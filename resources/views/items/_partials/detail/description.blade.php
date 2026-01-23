{{-- Item Description Partial --}}
{{-- Usage: @include('items._partials.detail.description', ['viewModel' => $viewModel]) --}}

@if ($viewModel->hasDescription)
    <div class="khezana-item-description">
        <h3 class="khezana-section-title-small">{{ __('items.fields.description') }}</h3>
        <p class="khezana-description-text">{{ $viewModel->description }}</p>
    </div>
@endif
