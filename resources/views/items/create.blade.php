@extends('layouts.app')

@section('title', __('items.actions.create') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-create-page">
        <div class="khezana-container">
            <div class="khezana-page-header">
                <h1 class="khezana-page-title">{{ __('items.actions.create') }}</h1>
                <p class="khezana-page-subtitle">
                    {{ __('items.messages.create_help') }}
                </p>
            </div>

            <div class="khezana-form-container">
                <form method="POST" action="{{ route('items.store') }}" class="khezana-form">
                    @csrf

                    <div class="khezana-form-group">
                        <label for="category_id" class="khezana-form-label">
                            {{ __('items.fields.category') }}
                            <span class="khezana-required">*</span>
                        </label>
                        <select name="category_id" id="category_id" class="khezana-form-input khezana-form-select" required
                            onchange="loadCategoryAttributes(this.value)">
                            <option value="">{{ __('common.ui.select_category') }}</option>
                            @foreach ($categories as $category)
                                @php
                                    $allAttrs = $category->getAllAttributes();
                                    $allAttrs->each(function ($attr) {
                                        if (
                                            $attr instanceof \App\Models\Attribute &&
                                            !$attr->relationLoaded('values')
                                        ) {
                                            $attr->load('values');
                                        }
                                    });
                                    $categoryAttributes = $allAttrs
                                        ->filter(function ($attr) {
                                            return $attr instanceof \App\Models\Attribute;
                                        })
                                        ->map(function ($attr) {
                                            return [
                                                'id' => $attr->id,
                                                'name' => $attr->name,
                                                'slug' => $attr->slug,
                                                'type' => $attr->type->value,
                                                'is_required' => $attr->is_required,
                                                'values' => $attr->values
                                                    ? $attr->values->pluck('value')->toArray()
                                                    : [],
                                            ];
                                        })
                                        ->toJson();
                                @endphp
                                <option value="{{ $category->id }}" data-attributes="{{ $categoryAttributes }}">
                                    {{ $category->name }}
                                </option>
                                @if ($category->children->count() > 0)
                                    @foreach ($category->children as $child)
                                        @php
                                            $childAllAttrs = $child->getAllAttributes();
                                            $childAllAttrs->each(function ($attr) {
                                                if (
                                                    $attr instanceof \App\Models\Attribute &&
                                                    !$attr->relationLoaded('values')
                                                ) {
                                                    $attr->load('values');
                                                }
                                            });
                                            $childAttributes = $childAllAttrs
                                                ->filter(function ($attr) {
                                                    return $attr instanceof \App\Models\Attribute;
                                                })
                                                ->map(function ($attr) {
                                                    return [
                                                        'id' => $attr->id,
                                                        'name' => $attr->name,
                                                        'slug' => $attr->slug,
                                                        'type' => $attr->type->value,
                                                        'is_required' => $attr->is_required,
                                                        'values' => $attr->values
                                                            ? $attr->values->pluck('value')->toArray()
                                                            : [],
                                                    ];
                                                })
                                                ->toJson();
                                        @endphp
                                        <option value="{{ $child->id }}" data-attributes="{{ $childAttributes }}">
                                            &nbsp;&nbsp;{{ $child->name }}
                                        </option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                        <p class="khezana-form-hint">{{ __('items.hints.category') }}</p>
                        @error('category_id')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="khezana-form-group">
                        <label class="khezana-form-label">
                            {{ __('items.fields.operation_type') }}
                            <span class="khezana-required">*</span>
                        </label>
                        <p class="khezana-form-hint">{{ __('items.hints.operation_type') }}</p>
                        <div class="khezana-filter-options">
                            <label class="khezana-filter-option">
                                <input type="radio" name="operation_type" value="sell" required
                                    {{ old('operation_type') === 'sell' ? 'checked' : '' }}>
                                <span>{{ __('items.operation_types.sell') }}</span>
                            </label>
                            <label class="khezana-filter-option">
                                <input type="radio" name="operation_type" value="rent" required
                                    {{ old('operation_type') === 'rent' ? 'checked' : '' }}>
                                <span>{{ __('items.operation_types.rent') }}</span>
                            </label>
                            <label class="khezana-filter-option">
                                <input type="radio" name="operation_type" value="donate" required
                                    {{ old('operation_type') === 'donate' ? 'checked' : '' }}>
                                <span>{{ __('items.operation_types.donate') }}</span>
                            </label>
                        </div>
                        @error('operation_type')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="khezana-form-group">
                        <label for="title" class="khezana-form-label">
                            {{ __('items.fields.title') }}
                            <span class="khezana-required">*</span>
                        </label>
                        <input type="text" name="title" id="title" class="khezana-form-input"
                            value="{{ old('title') }}" placeholder="{{ __('items.placeholders.title') }}" required
                            maxlength="255">
                        @error('title')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="khezana-form-group">
                        <label for="description" class="khezana-form-label">
                            {{ __('items.fields.description') }}
                        </label>
                        <p class="khezana-form-hint">{{ __('items.hints.description') }}</p>
                        <textarea name="description" id="description" class="khezana-form-input khezana-form-textarea" rows="5"
                            placeholder="{{ __('items.placeholders.description') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="khezana-form-row">
                        <div class="khezana-form-group">
                            <label for="price" class="khezana-form-label">
                                {{ __('items.fields.price') }}
                            </label>
                            <p class="khezana-form-hint">{{ __('items.hints.price') }}</p>
                            <input type="number" step="0.01" name="price" id="price" class="khezana-form-input"
                                value="{{ old('price') }}" placeholder="{{ __('items.placeholders.price') }}">
                            @error('price')
                                <span class="khezana-form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="khezana-form-group">
                            <label for="deposit_amount" class="khezana-form-label">
                                {{ __('items.fields.deposit_amount') }}
                            </label>
                            <p class="khezana-form-hint">{{ __('items.hints.deposit_amount') }}</p>
                            <input type="number" step="0.01" name="deposit_amount" id="deposit_amount"
                                class="khezana-form-input" value="{{ old('deposit_amount') }}"
                                placeholder="{{ __('items.placeholders.deposit_amount') }}">
                            @error('deposit_amount')
                                <span class="khezana-form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="khezana-form-group khezana-form-checkbox">
                        <label class="khezana-form-label">
                            <input type="checkbox" name="is_available" value="1"
                                {{ old('is_available', true) ? 'checked' : '' }}>
                            {{ __('items.fields.is_available') }}
                        </label>
                        <p class="khezana-form-hint">{{ __('items.hints.is_available') }}</p>
                        @error('is_available')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div id="attributesContainer" class="khezana-attributes-container" style="display: none;">
                        <h3 class="khezana-section-title-small">{{ __('items.fields.attributes') }}</h3>
                        <div id="attributesFields"></div>
                    </div>

                    <div class="khezana-info-box">
                        <div class="khezana-info-icon">ℹ️</div>
                        <div class="khezana-info-content">
                            <p class="khezana-info-text">
                                {{ __('items.messages.review_notice') }}
                            </p>
                        </div>
                    </div>

                    <div class="khezana-form-actions">
                        <button type="submit" class="khezana-btn khezana-btn-primary khezana-btn-large">
                            {{ __('items.actions.submit_for_approval') }}
                        </button>
                        <a href="{{ route('items.index') }}" class="khezana-btn khezana-btn-secondary">
                            {{ __('common.actions.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function loadCategoryAttributes(categoryId) {
            const select = document.getElementById('category_id');
            const selectedOption = select.options[select.selectedIndex];
            const attributesContainer = document.getElementById('attributesContainer');
            const attributesFields = document.getElementById('attributesFields');

            if (!categoryId || !selectedOption) {
                attributesContainer.style.display = 'none';
                attributesFields.innerHTML = '';
                return;
            }

            try {
                const attributes = JSON.parse(selectedOption.getAttribute('data-attributes') || '[]');

                if (attributes.length === 0) {
                    attributesContainer.style.display = 'none';
                    attributesFields.innerHTML = '';
                    return;
                }

                attributesContainer.style.display = 'block';
                attributesFields.innerHTML = '';

                attributes.forEach(attribute => {
                    const attributeDiv = document.createElement('div');
                    attributeDiv.className = 'khezana-form-group';

                    const label = document.createElement('label');
                    label.className = 'khezana-form-label';
                    label.htmlFor = `attributes[${attribute.slug}]`;
                    label.textContent = attribute.name;
                    if (attribute.is_required) {
                        label.innerHTML += ' <span class="khezana-required">*</span>';
                    }

                    let input;

                    if (attribute.type === 'select') {
                        input = document.createElement('select');
                        input.className = 'khezana-form-input khezana-form-select';
                        input.name = `attributes[${attribute.slug}]`;
                        input.id = `attributes[${attribute.slug}]`;
                        if (attribute.is_required) {
                            input.required = true;
                        }

                        const emptyOption = document.createElement('option');
                        emptyOption.value = '';
                        emptyOption.textContent = '{{ __('common.ui.choose') }}';
                        input.appendChild(emptyOption);

                        if (attribute.values && attribute.values.length > 0) {
                            attribute.values.forEach(value => {
                                const option = document.createElement('option');
                                option.value = value;
                                option.textContent = value;
                                input.appendChild(option);
                            });
                        }
                    } else if (attribute.type === 'number') {
                        input = document.createElement('input');
                        input.type = 'number';
                        input.className = 'khezana-form-input';
                        input.name = `attributes[${attribute.slug}]`;
                        input.id = `attributes[${attribute.slug}]`;
                        if (attribute.is_required) {
                            input.required = true;
                        }
                    } else {
                        input = document.createElement('input');
                        input.type = 'text';
                        input.className = 'khezana-form-input';
                        input.name = `attributes[${attribute.slug}]`;
                        input.id = `attributes[${attribute.slug}]`;
                        if (attribute.is_required) {
                            input.required = true;
                        }
                    }

                    attributeDiv.appendChild(label);
                    attributeDiv.appendChild(input);
                    attributesFields.appendChild(attributeDiv);
                });
            } catch (e) {
                console.error('Error loading attributes:', e);
                attributesContainer.style.display = 'none';
            }
        }

        @if (old('category_id'))
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('category_id').value = {{ old('category_id') }};
                loadCategoryAttributes({{ old('category_id') }});
            });
        @endif
    </script>
@endsection
