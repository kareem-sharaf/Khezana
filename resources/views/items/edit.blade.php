@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', __('items.actions.edit') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-create-page">
        <div class="khezana-container">
            <div class="khezana-page-header">
                <h1 class="khezana-page-title">{{ __('items.actions.edit') }}</h1>
                <p class="khezana-page-subtitle">
                    {{ __('items.messages.create_help') }}
                </p>
            </div>

            <div class="khezana-form-container">
                <form method="POST" action="{{ route('items.update', $item) }}" class="khezana-form"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}
                                    data-attributes="{{ $categoryAttributes }}">
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
                                        <option value="{{ $child->id }}"
                                            {{ old('category_id', $item->category_id) == $child->id ? 'selected' : '' }}
                                            data-attributes="{{ $childAttributes }}">
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
                                    {{ old('operation_type', $item->operation_type->value) === 'sell' ? 'checked' : '' }}>
                                <span>{{ __('items.operation_types.sell') }}</span>
                            </label>
                            <label class="khezana-filter-option">
                                <input type="radio" name="operation_type" value="rent" required
                                    {{ old('operation_type', $item->operation_type->value) === 'rent' ? 'checked' : '' }}>
                                <span>{{ __('items.operation_types.rent') }}</span>
                            </label>
                            <label class="khezana-filter-option">
                                <input type="radio" name="operation_type" value="donate" required
                                    {{ old('operation_type', $item->operation_type->value) === 'donate' ? 'checked' : '' }}>
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
                            value="{{ old('title', $item->title) }}" placeholder="{{ __('items.placeholders.title') }}"
                            required maxlength="255">
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
                            placeholder="{{ __('items.placeholders.description') }}">{{ old('description', $item->description) }}</textarea>
                        @error('description')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Condition Group -->
                    <div class="khezana-form-group">
                        <label class="khezana-form-label">
                            <span class="khezana-field-icon">🏷️</span>
                            {{ __('items.fields.condition') }}
                            <span class="khezana-required">*</span>
                        </label>
                        <p class="khezana-form-hint">{{ __('items.hints.condition') }}</p>
                        <div class="khezana-filter-options">
                            <label class="khezana-filter-option">
                                <input type="radio" name="condition" value="new" required
                                    {{ old('condition', $item->condition) === 'new' ? 'checked' : '' }}>
                                <span>{{ __('items.conditions.new') }}</span>
                                <small class="khezana-option-hint">{{ __('items.conditions.new_hint') }}</small>
                            </label>
                            <label class="khezana-filter-option">
                                <input type="radio" name="condition" value="used" required
                                    {{ old('condition', $item->condition) === 'used' ? 'checked' : '' }}>
                                <span>{{ __('items.conditions.used') }}</span>
                                <small class="khezana-option-hint">{{ __('items.conditions.used_hint') }}</small>
                            </label>
                        </div>
                        @error('condition')
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
                                value="{{ old('price', $item->price) }}"
                                placeholder="{{ __('items.placeholders.price') }}">
                            @error('price')
                                <span class="khezana-form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="khezana-form-group" id="deposit_amount_group" style="display: none;">
                            <label for="deposit_amount" class="khezana-form-label">
                                {{ __('items.fields.deposit_amount') }}
                            </label>
                            <p class="khezana-form-hint">{{ __('items.hints.deposit_amount') }}</p>
                            <input type="number" step="0.01" name="deposit_amount" id="deposit_amount"
                                class="khezana-form-input" value="{{ old('deposit_amount', $item->deposit_amount) }}"
                                placeholder="{{ __('items.placeholders.deposit_amount') }}">
                            @error('deposit_amount')
                                <span class="khezana-form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="khezana-form-group khezana-form-checkbox">
                        <label class="khezana-form-label">
                            <input type="checkbox" name="is_available" value="1"
                                {{ old('is_available', $item->is_available) ? 'checked' : '' }}>
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

                    <div class="khezana-form-group">
                        <label for="images" class="khezana-form-label">
                            {{ __('items.fields.images') }}
                        </label>
                        <p class="khezana-form-hint">{{ __('items.hints.images') }}</p>
                        <input type="file" name="images[]" id="images" class="khezana-form-input"
                            accept="image/jpeg,image/jpg,image/png" multiple>
                        @error('images')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror
                        @error('images.*')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror

                        {{-- Show existing images --}}
                        @if ($item->images->count() > 0)
                            <div class="khezana-existing-images" style="margin-top: var(--khezana-spacing-md);">
                                <p
                                    style="font-size: var(--khezana-font-size-sm); color: var(--khezana-text-light); margin-bottom: var(--khezana-spacing-sm);">
                                    {{ __('items.messages.existing_images') }}
                                </p>
                                <div class="khezana-image-preview-grid">
                                    @foreach ($item->images as $image)
                                        <div class="khezana-image-preview-item">
                                            <img src="{{ Storage::url($image->path) }}"
                                                alt="Image {{ $loop->index + 1 }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Image Preview Container for new uploads --}}
                        <div id="imagePreviewContainer" class="khezana-image-preview-container"
                            style="display: none; margin-top: var(--khezana-spacing-md);">
                            <p
                                style="font-size: var(--khezana-font-size-sm); color: var(--khezana-text-light); margin-bottom: var(--khezana-spacing-sm);">
                                {{ __('items.messages.new_images') }}
                            </p>
                            <div id="imagePreviewGrid" class="khezana-image-preview-grid"></div>
                        </div>
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
                            {{ __('common.actions.save') }}
                        </button>
                        <a href="{{ route('items.show', $item) }}" class="khezana-btn khezana-btn-secondary">
                            {{ __('common.actions.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Translation map for common attribute names
        const attributeTranslations = {
            'size': '{{ __('attributes.common_names.size') }}',
            'color': '{{ __('attributes.common_names.color') }}',
            'condition': '{{ __('attributes.common_names.condition') }}',
            'fabric': '{{ __('attributes.common_names.fabric') }}',
            'material': '{{ __('attributes.common_names.material') }}',
            'brand': '{{ __('attributes.common_names.brand') }}',
            'style': '{{ __('attributes.common_names.style') }}',
            'gender': '{{ __('attributes.common_names.gender') }}',
            'age_group': '{{ __('attributes.common_names.age_group') }}',
        };

        function translateAttributeName(name) {
            const lowerName = name.toLowerCase().trim();
            return attributeTranslations[lowerName] || name;
        }

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

                // Get existing attribute values
                const existingAttributes = @json($item->itemAttributes->pluck('value', 'attribute.slug')->toArray());

                attributes.forEach(attribute => {
                    const attributeDiv = document.createElement('div');
                    attributeDiv.className = 'khezana-form-group';

                    const label = document.createElement('label');
                    label.className = 'khezana-form-label';
                    label.htmlFor = `attributes[${attribute.slug}]`;
                    label.textContent = translateAttributeName(attribute.name);
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
                                if (existingAttributes[attribute.slug] === value) {
                                    option.selected = true;
                                }
                                input.appendChild(option);
                            });
                        }
                    } else if (attribute.type === 'number') {
                        input = document.createElement('input');
                        input.type = 'number';
                        input.className = 'khezana-form-input';
                        input.name = `attributes[${attribute.slug}]`;
                        input.id = `attributes[${attribute.slug}]`;
                        input.value = existingAttributes[attribute.slug] || '';
                        if (attribute.is_required) {
                            input.required = true;
                        }
                    } else {
                        input = document.createElement('input');
                        input.type = 'text';
                        input.className = 'khezana-form-input';
                        input.name = `attributes[${attribute.slug}]`;
                        input.id = `attributes[${attribute.slug}]`;
                        input.value = existingAttributes[attribute.slug] || '';
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

        // Show/hide deposit amount field based on operation type
        function toggleDepositAmountField() {
            const operationTypeRadios = document.querySelectorAll('input[name="operation_type"]');
            const depositAmountGroup = document.getElementById('deposit_amount_group');
            const depositAmountInput = document.getElementById('deposit_amount');

            if (!depositAmountGroup || !depositAmountInput) return;

            // Check which operation type is selected
            let selectedOperationType = null;
            operationTypeRadios.forEach(radio => {
                if (radio.checked) {
                    selectedOperationType = radio.value;
                }
            });

            // Show deposit amount only for rent operation
            if (selectedOperationType === 'rent') {
                depositAmountGroup.style.display = 'block';
                depositAmountInput.setAttribute('required', 'required');
            } else {
                depositAmountGroup.style.display = 'none';
                depositAmountInput.removeAttribute('required');
                // Don't clear value on edit, just hide the field
            }
        }

        // Load attributes on page load if category is selected
        document.addEventListener('DOMContentLoaded', function() {
            const categoryId = document.getElementById('category_id').value;
            if (categoryId) {
                loadCategoryAttributes(categoryId);
            }

            // Add event listeners to operation type radios
            const operationTypeRadios = document.querySelectorAll('input[name="operation_type"]');
            operationTypeRadios.forEach(radio => {
                radio.addEventListener('change', toggleDepositAmountField);
            });

            // Check initial state based on current item operation type
            toggleDepositAmountField();
        });

        // Image upload preview
        (function() {
            let imageInput, previewContainer, previewGrid;

            function updatePreview() {
                if (!imageInput || !previewContainer || !previewGrid) return;

                const files = Array.from(imageInput.files);
                previewGrid.innerHTML = '';

                if (files.length === 0) {
                    previewContainer.style.display = 'none';
                    return;
                }

                previewContainer.style.display = 'block';

                files.forEach((file, index) => {
                    // Validate file type
                    if (!file.type.match('image.*')) {
                        return;
                    }

                    // Validate file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'khezana-image-preview-item';
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}">
                            <button type="button" class="khezana-image-remove-btn" data-index="${index}" aria-label="{{ __('common.actions.remove') }}">
                                <span>×</span>
                            </button>
                        `;
                        previewGrid.appendChild(previewItem);

                        // Add remove button functionality
                        const removeBtn = previewItem.querySelector('.khezana-image-remove-btn');
                        removeBtn.addEventListener('click', function() {
                            removeImage(index);
                        });
                    };
                    reader.readAsDataURL(file);
                });
            }

            function removeImage(index) {
                const dt = new DataTransfer();
                const files = Array.from(imageInput.files);

                files.forEach((file, i) => {
                    if (i !== index) {
                        dt.items.add(file);
                    }
                });

                imageInput.files = dt.files;
                updatePreview();
            }

            document.addEventListener('DOMContentLoaded', function() {
                imageInput = document.getElementById('images');
                previewContainer = document.getElementById('imagePreviewContainer');
                previewGrid = document.getElementById('imagePreviewGrid');

                if (!imageInput || !previewContainer || !previewGrid) return;

                imageInput.addEventListener('change', updatePreview);
            });
        })();
    </script>
@endsection
