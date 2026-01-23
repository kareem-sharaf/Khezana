@extends('layouts.app')

@section('title', __('requests.actions.create') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-create-page">
        <div class="khezana-container">
            <!-- Page Header -->
            <div class="khezana-page-header">
                <h1 class="khezana-page-title">{{ __('requests.actions.create') }}</h1>
                <p class="khezana-page-subtitle">
                    {{ __('requests.info_page.hero_subtitle') }}
                </p>
            </div>

            <!-- Form -->
            <div class="khezana-form-container">
                <form method="POST" action="{{ route('requests.store') }}" class="khezana-form">
                    @csrf

                    <!-- Category -->
                    <div class="khezana-form-group">
                        <label for="category_id" class="khezana-form-label">
                            {{ __('requests.fields.category') }}
                            <span class="khezana-required">*</span>
                        </label>
                        <p class="khezana-form-hint">{{ __('requests.hints.category') }}</p>
                        @include('components.category-select', [
                            'categories' => $categories,
                            'name' => 'category_id',
                            'id' => 'category_id',
                            'selected' => old('category_id'),
                            'required' => true,
                            'placeholder' => __('common.ui.select_category'),
                            'showAllOption' => false,
                            'attributes' => true,
                            'onchange' => 'loadCategoryAttributes(this.value)',
                        ])
                        @error('category_id')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="khezana-form-group">
                        <label for="title" class="khezana-form-label">
                            {{ __('requests.fields.title') }}
                            <span class="khezana-required">*</span>
                        </label>
                        <p class="khezana-form-hint">{{ __('requests.hints.title') }}</p>
                        <input type="text" name="title" id="title" class="khezana-form-input"
                            value="{{ old('title') }}" placeholder="مثال: أحتاج حذاء رجالي لمناسبة عرس، مقاس 42" required
                            maxlength="255">
                        @error('title')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="khezana-form-group">
                        <label for="description" class="khezana-form-label">
                            {{ __('requests.fields.description') }}
                        </label>
                        <p class="khezana-form-hint">{{ __('requests.hints.description') }}</p>
                        <textarea name="description" id="description" class="khezana-form-input khezana-form-textarea" rows="5"
                            placeholder="اكتب وصفاً تفصيلياً لطلبك... (اختياري)">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Dynamic Attributes -->
                    <div id="attributesContainer" class="khezana-attributes-container" style="display: none;">
                        <h3 class="khezana-section-title-small">المواصفات</h3>
                        <div id="attributesFields"></div>
                    </div>

                    <!-- Info Box -->
                    <div class="khezana-info-box">
                        <div class="khezana-info-icon">ℹ️</div>
                        <div class="khezana-info-content">
                            <p class="khezana-info-text">
                                <strong>ملاحظة:</strong> سيتم مراجعة طلبك من قبل فريقنا قبل النشر لضمان الجودة والأمان.
                            </p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="khezana-form-actions">
                        <button type="submit" class="khezana-btn khezana-btn-primary khezana-btn-large">
                            {{ __('requests.actions.submit_for_approval') }}
                        </button>
                        <a href="{{ route('requests.index') }}" class="khezana-btn khezana-btn-secondary">
                            {{ __('common.actions.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Simple JavaScript for Dynamic Attributes -->
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

                        // Add empty option
                        const emptyOption = document.createElement('option');
                        emptyOption.value = '';
                        emptyOption.textContent = '{{ __('common.ui.choose') }}';
                        input.appendChild(emptyOption);

                        // Add values if available
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

        // Load attributes if category is pre-selected (from old input)
        @if (old('category_id'))
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('category_id').value = {{ old('category_id') }};
                loadCategoryAttributes({{ old('category_id') }});
            });
        @endif
    </script>
@endsection
