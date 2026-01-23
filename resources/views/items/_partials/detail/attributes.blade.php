{{-- Item Attributes Partial --}}
{{-- Usage: @include('items._partials.detail.attributes', ['viewModel' => $viewModel]) --}}

@if ($viewModel->hasAttributes)
    <div class="khezana-item-attributes">
        <h3 class="khezana-section-title-small">{{ __('items.fields.attributes') }}</h3>
        <div class="khezana-attributes-grid">
            @foreach ($viewModel->attributes as $itemAttribute)
                @php
                    $originalName = $itemAttribute->originalName ?? $itemAttribute->attribute->name ?? $itemAttribute->name ?? '';
                    $attributeName = $itemAttribute->name ?? translate_attribute_name($originalName);
                    $attributeValue = $itemAttribute->formattedValue ?? $itemAttribute->value ?? '';
                @endphp
                <div class="khezana-attribute-item">
                    <span class="khezana-attribute-name">{{ $attributeName }}:</span>
                    <span class="khezana-attribute-value">{{ $attributeValue }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif
