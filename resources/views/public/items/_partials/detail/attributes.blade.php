{{-- Public Item Attributes Partial --}}
{{-- Usage: @include('public.items._partials.detail.attributes', ['viewModel' => $viewModel]) --}}

@if ($viewModel->hasAttributes)
    <div class="khezana-item-attributes">
        <h3 class="khezana-section-title-small">{{ __('common.ui.specifications') }}</h3>
        <div class="khezana-attributes-grid">
            @foreach ($viewModel->attributes as $attribute)
                <div class="khezana-attribute-item">
                    <span class="khezana-attribute-name">{{ $attribute->name }}:</span>
                    <span class="khezana-attribute-value">{{ $attribute->formattedValue ?? $attribute->value }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif
