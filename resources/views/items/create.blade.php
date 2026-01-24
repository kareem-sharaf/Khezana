@extends('layouts.app')

@section('title', __('items.actions.create') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-create-page">
        {{-- Pre-Creation Notice Modal (shown until user acknowledges) --}}
        <div id="preCreationNotice" class="khezana-notice-overlay" role="dialog" aria-modal="true"
            aria-labelledby="preCreationNoticeTitle" aria-describedby="preCreationNoticeDesc">
            <div class="khezana-notice-modal">
                <h2 id="preCreationNoticeTitle" class="khezana-notice-title">
                    {{ __('items.pre_creation_notice.title') }}
                </h2>
                <ul id="preCreationNoticeDesc" class="khezana-notice-list">
                    @foreach ($preCreationRules as $rule)
                        <li class="khezana-notice-item">
                            <span class="khezana-notice-icon" aria-hidden="true">{{ $rule['icon'] }}</span>
                            <span>{{ $rule['text'] }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="khezana-notice-actions">
                    <a href="{{ route('items.index') }}" id="noticeCancel" class="khezana-btn khezana-btn-secondary">
                        {{ __('items.pre_creation_notice.btn_cancel') }}
                    </a>
                    <button type="button" id="noticeContinue" class="khezana-btn khezana-btn-primary khezana-btn-large">
                        {{ __('items.pre_creation_notice.btn_continue') }}
                    </button>
                </div>
            </div>
        </div>

        <div id="createFormWrap" class="khezana-container khezana-create-form-wrap" hidden>
            <x-breadcrumb :items="[
                ['label' => __('common.ui.my_items_page'), 'url' => route('items.index')],
                ['label' => __('items.actions.create'), 'url' => null],
            ]" />

            <div class="khezana-page-header">
                <h1 class="khezana-page-title">{{ __('items.actions.create') }}</h1>
                <p class="khezana-page-subtitle">
                    {{ __('items.messages.create_help') }}
                </p>
            </div>

            <div class="khezana-form-container">
                @if ($errors->any())
                    <div class="khezana-alert khezana-alert--error khezana-mb-md">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <p id="draftSavedIndicator" class="khezana-form-hint khezana-draft-indicator"
                    style="display:none; margin-bottom:1rem;" aria-live="polite">
                    <span class="khezana-draft-indicator-icon">💾</span>
                    {{ __('items.messages.draft_saved') }}
                </p>

                {{-- Progress Indicator --}}
                <div class="khezana-form-progress">
                    <div class="khezana-form-progress-steps">
                        <div class="khezana-form-progress-step" data-step="1">
                            <div class="khezana-form-progress-step-number">1</div>
                            <div class="khezana-form-progress-step-label">{{ __('items.sections.basic_info') }}</div>
                        </div>
                        <div class="khezana-form-progress-step" data-step="2">
                            <div class="khezana-form-progress-step-number">2</div>
                            <div class="khezana-form-progress-step-label">{{ __('items.sections.details') }}</div>
                        </div>
                        <div class="khezana-form-progress-step" data-step="3">
                            <div class="khezana-form-progress-step-number">3</div>
                            <div class="khezana-form-progress-step-label">{{ __('items.fields.images') }}</div>
                        </div>
                    </div>
                    <div class="khezana-form-progress-bar">
                        <div class="khezana-form-progress-bar-fill" id="progressBarFill"></div>
                    </div>
                </div>

                <form method="POST" action="{{ route('items.store') }}" class="khezana-form" id="itemCreateForm"
                    enctype="multipart/form-data">
                    @csrf

                    {{-- Section 1: Basic Information --}}
                    <div class="khezana-form-section">
                        <div class="khezana-form-section-header">
                            <h2 class="khezana-form-section-title">
                                <span class="khezana-form-section-icon">👕</span>
                                {{ __('items.sections.basic_info') }}
                            </h2>
                            <p class="khezana-form-section-description">{{ __('items.sections.basic_info_desc') }}</p>
                        </div>

                        <div class="khezana-form-section-content">
                            <div class="khezana-form-group">
                                <label for="category_id" class="khezana-form-label">
                                    <span class="khezana-form-label-icon">👗</span>
                                    {{ __('items.fields.category') }}
                                    <span class="khezana-required">*</span>
                                </label>
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
                                <p class="khezana-form-hint">{{ __('items.hints.category') }}</p>
                                @error('category_id')
                                    <span class="khezana-form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="khezana-form-group">
                                <label class="khezana-form-label">
                                    <span class="khezana-form-label-icon">💼</span>
                                    {{ __('items.fields.operation_type') }}
                                    <span class="khezana-required">*</span>
                                </label>
                                <p class="khezana-form-hint">{{ __('items.hints.operation_type') }}</p>
                                <div class="khezana-filter-options khezana-filter-options--enhanced">
                                    <label class="khezana-filter-option khezana-filter-option--card">
                                        <input type="radio" name="operation_type" value="sell" required
                                            {{ old('operation_type') === 'sell' ? 'checked' : '' }}>
                                        <div class="khezana-filter-option-content">
                                            <span class="khezana-filter-option-icon">💵</span>
                                            <span
                                                class="khezana-filter-option-label">{{ __('items.operation_types.sell') }}</span>
                                        </div>
                                    </label>
                                    <label class="khezana-filter-option khezana-filter-option--card">
                                        <input type="radio" name="operation_type" value="rent" required
                                            {{ old('operation_type') === 'rent' ? 'checked' : '' }}>
                                        <div class="khezana-filter-option-content">
                                            <span class="khezana-filter-option-icon">👔</span>
                                            <span
                                                class="khezana-filter-option-label">{{ __('items.operation_types.rent') }}</span>
                                        </div>
                                    </label>
                                    <label class="khezana-filter-option khezana-filter-option--card">
                                        <input type="radio" name="operation_type" value="donate" required
                                            {{ old('operation_type') === 'donate' ? 'checked' : '' }}>
                                        <div class="khezana-filter-option-content">
                                            <span class="khezana-filter-option-icon">🎀</span>
                                            <span
                                                class="khezana-filter-option-label">{{ __('items.operation_types.donate') }}</span>
                                        </div>
                                    </label>
                                </div>
                                @error('operation_type')
                                    <span class="khezana-form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="khezana-form-group">
                                <label for="title" class="khezana-form-label">
                                    <span class="khezana-form-label-icon">✏️</span>
                                    {{ __('items.fields.title') }}
                                    <span class="khezana-required">*</span>
                                </label>
                                <input type="text" name="title" id="title"
                                    class="khezana-form-input khezana-form-input--enhanced" value="{{ old('title') }}"
                                    placeholder="{{ __('items.placeholders.title') }}" required maxlength="255">
                                @error('title')
                                    <span class="khezana-form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="khezana-form-group">
                                <label for="description" class="khezana-form-label">
                                    <span class="khezana-form-label-icon">📄</span>
                                    {{ __('items.fields.description') }}
                                </label>
                                <p class="khezana-form-hint">{{ __('items.hints.description') }}</p>
                                <textarea name="description" id="description"
                                    class="khezana-form-input khezana-form-textarea khezana-form-textarea--enhanced" rows="5"
                                    placeholder="{{ __('items.placeholders.description') }}">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="khezana-form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Details --}}
                    <div class="khezana-form-section">
                        <div class="khezana-form-section-header">
                            <h2 class="khezana-form-section-title">
                                <span class="khezana-form-section-icon">🔍</span>
                                {{ __('items.sections.details') }}
                            </h2>
                            <p class="khezana-form-section-description">{{ __('items.sections.details_desc') }}</p>
                        </div>

                        <div class="khezana-form-section-content">
                            <div class="khezana-form-group">
                                <label class="khezana-form-label">
                                    <span class="khezana-form-label-icon">🏷️</span>
                                    {{ __('items.fields.condition') }}
                                    <span class="khezana-required">*</span>
                                </label>
                                <p class="khezana-form-hint">{{ __('items.hints.condition') }}</p>
                                <div class="khezana-filter-options khezana-filter-options--enhanced">
                                    <label class="khezana-filter-option khezana-filter-option--card">
                                        <input type="radio" name="condition" value="new" required
                                            {{ old('condition') === 'new' ? 'checked' : '' }}>
                                        <div class="khezana-filter-option-content">
                                            <span class="khezana-filter-option-icon">✨</span>
                                            <div class="khezana-filter-option-text">
                                                <span
                                                    class="khezana-filter-option-label">{{ __('items.conditions.new') }}</span>
                                                <small
                                                    class="khezana-option-hint">{{ __('items.conditions.new_hint') }}</small>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="khezana-filter-option khezana-filter-option--card">
                                        <input type="radio" name="condition" value="used" required
                                            {{ old('condition') === 'used' ? 'checked' : '' }}>
                                        <div class="khezana-filter-option-content">
                                            <span class="khezana-filter-option-icon">♻️</span>
                                            <div class="khezana-filter-option-text">
                                                <span
                                                    class="khezana-filter-option-label">{{ __('items.conditions.used') }}</span>
                                                <small
                                                    class="khezana-option-hint">{{ __('items.conditions.used_hint') }}</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @error('condition')
                                    <span class="khezana-form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="khezana-form-row khezana-form-row--enhanced">
                                <div class="khezana-form-group">
                                    <label for="price" class="khezana-form-label">
                                        <span class="khezana-form-label-icon">💰</span>
                                        {{ __('items.fields.price') }}
                                    </label>
                                    <p class="khezana-form-hint">{{ __('items.hints.price') }}</p>
                                    <div class="khezana-form-input-wrapper">
                                        <input type="number" step="0.01" name="price" id="price"
                                            class="khezana-form-input khezana-form-input--enhanced"
                                            value="{{ old('price') }}"
                                            placeholder="{{ __('items.placeholders.price') }}">
                                        <span class="khezana-form-input-suffix">ل.س</span>
                                    </div>
                                    @error('price')
                                        <span class="khezana-form-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="khezana-form-group" id="deposit_amount_group"
                                    style="display: {{ old('operation_type') === 'rent' ? 'block' : 'none' }};">
                                    <label for="deposit_amount" class="khezana-form-label">
                                        <span class="khezana-form-label-icon">💳</span>
                                        {{ __('items.fields.deposit_amount') }}
                                        <span class="khezana-required">*</span>
                                    </label>
                                    <p class="khezana-form-hint">{{ __('items.hints.deposit_amount') }}</p>
                                    <div class="khezana-form-input-wrapper">
                                        <input type="number" step="0.01" name="deposit_amount" id="deposit_amount"
                                            class="khezana-form-input khezana-form-input--enhanced"
                                            value="{{ old('deposit_amount') }}"
                                            placeholder="{{ __('items.placeholders.deposit_amount') }}"
                                            {{ old('operation_type') === 'rent' ? 'required' : '' }}>
                                        <span class="khezana-form-input-suffix">ل.س</span>
                                    </div>
                                    @error('deposit_amount')
                                        <span class="khezana-form-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Attributes --}}
                    <div id="attributesContainer" class="khezana-form-section" style="display: none;">
                        <div class="khezana-form-section-header">
                            <h2 class="khezana-form-section-title">
                                <span class="khezana-form-section-icon">🎨</span>
                                {{ __('items.fields.attributes') }}
                            </h2>
                            <p class="khezana-form-section-description">{{ __('items.sections.attributes_desc') }}</p>
                        </div>
                        <div class="khezana-form-section-content">
                            <div id="attributesFields"></div>
                        </div>
                    </div>

                    {{-- Section 4: Images --}}
                    <div class="khezana-form-section">
                        <div class="khezana-form-section-header">
                            <h2 class="khezana-form-section-title">
                                <span class="khezana-form-section-icon">📷</span>
                                {{ __('items.fields.images') }}
                            </h2>
                            <p class="khezana-form-section-description">{{ __('items.sections.images_desc') }}</p>
                        </div>
                        <div class="khezana-form-section-content">
                            <div class="khezana-form-group">
                                <p class="khezana-form-hint">{{ __('items.hints.images') }}</p>
                                <div id="imageDropZone" class="khezana-image-drop-zone khezana-image-drop-zone--enhanced"
                                    aria-label="{{ __('items.hints.images') }}">
                                    <div class="khezana-image-drop-zone-content">
                                        <span class="khezana-image-drop-zone-icon">📸</span>
                                        <span
                                            class="khezana-image-drop-zone__label">{{ __('items.hints.drop_images') }}</span>
                                        <span
                                            class="khezana-image-drop-zone-hint">{{ __('items.hints.click_or_drag') }}</span>
                                    </div>
                                    <input type="file" name="images[]" id="images" class="khezana-form-input"
                                        accept="image/jpeg,image/jpg,image/png" capture="environment" multiple>
                                </div>
                                @error('images')
                                    <span class="khezana-form-error">{{ $message }}</span>
                                @enderror
                                @error('images.*')
                                    <span class="khezana-form-error">{{ $message }}</span>
                                @enderror

                                {{-- Image Preview Container --}}
                                <div id="imagePreviewContainer" class="khezana-image-preview-container"
                                    style="display: none;">
                                    <div id="imagePreviewGrid" class="khezana-image-preview-grid"></div>
                                </div>
                            </div>
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

                        // Add placeholder and helper for size attribute
                        if (attribute.slug === 'size') {
                            input.placeholder = '{{ __('attributes.placeholders.size') }}';
                        }
                    }

                    attributeDiv.appendChild(label);
                    attributeDiv.appendChild(input);

                    // Add helper text for size attribute after input
                    if (attribute.slug === 'size' && attribute.type === 'text') {
                        const helper = document.createElement('p');
                        helper.className = 'khezana-form-hint';
                        helper.textContent = '{{ __('attributes.helpers.size') }}';
                        attributeDiv.appendChild(helper);
                    }

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
                depositAmountInput.value = '';
            }
        }

        // Add event listeners to operation type radios
        document.addEventListener('DOMContentLoaded', function() {
            const operationTypeRadios = document.querySelectorAll('input[name="operation_type"]');
            operationTypeRadios.forEach(radio => {
                radio.addEventListener('change', toggleDepositAmountField);
            });

            // Check initial state (in case of old input or default value)
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

            function addFiles(files) {
                if (!imageInput || !files || !files.length) return;
                const dt = new DataTransfer();
                const existing = Array.from(imageInput.files || []);
                existing.forEach(function(f) {
                    dt.items.add(f);
                });
                for (let i = 0; i < files.length; i++) {
                    if (files[i].type.match('image.*') && files[i].size <= 5 * 1024 * 1024) dt.items.add(files[i]);
                }
                imageInput.files = dt.files;
                updatePreview();
            }

            document.addEventListener('DOMContentLoaded', function() {
                imageInput = document.getElementById('images');
                previewContainer = document.getElementById('imagePreviewContainer');
                previewGrid = document.getElementById('imagePreviewGrid');
                const dropZone = document.getElementById('imageDropZone');

                if (!imageInput || !previewContainer || !previewGrid) return;

                imageInput.addEventListener('change', updatePreview);

                if (dropZone) {
                    ['dragenter', 'dragover'].forEach(function(ev) {
                        dropZone.addEventListener(ev, function(e) {
                            e.preventDefault();
                            dropZone.classList.add('khezana-image-drop-zone--dragover');
                        });
                    });
                    ['dragleave', 'drop'].forEach(function(ev) {
                        dropZone.addEventListener(ev, function(e) {
                            e.preventDefault();
                            dropZone.classList.remove('khezana-image-drop-zone--dragover');
                        });
                    });
                    dropZone.addEventListener('drop', function(e) {
                        addFiles(e.dataTransfer.files);
                    });
                }
            });
        })();

        (function() {
            var overlay = document.getElementById('preCreationNotice');
            var formWrap = document.getElementById('createFormWrap');
            var btnContinue = document.getElementById('noticeContinue');
            var cancelUrl = '{{ route('items.index') }}';
            var firstField = document.getElementById('category_id');

            function hideNoticeShowForm() {
                overlay.setAttribute('aria-hidden', 'true');
                formWrap.removeAttribute('hidden');
                if (firstField) firstField.focus();
            }

            function onContinue() {
                hideNoticeShowForm();
                document.removeEventListener('keydown', onEscape);
            }

            function onEscape(e) {
                if (e.key === 'Escape' && overlay.getAttribute('aria-hidden') !== 'true') {
                    e.preventDefault();
                    window.location.href = cancelUrl;
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Always show the notice - user must manually dismiss it
                overlay.removeAttribute('aria-hidden');
                formWrap.setAttribute('hidden', '');
                document.addEventListener('keydown', onEscape);
                if (btnContinue) btnContinue.focus();
                btnContinue.addEventListener('click', onContinue);
            });
        })();

        // Form Progress Tracker
        (function() {
            const form = document.getElementById('itemCreateForm');
            const progressBarFill = document.getElementById('progressBarFill');
            const progressSteps = document.querySelectorAll('.khezana-form-progress-step');

            function updateProgress() {
                if (!form || !progressBarFill) return;

                const requiredFields = form.querySelectorAll('[required]');
                const filledFields = Array.from(requiredFields).filter(field => {
                    if (field.type === 'radio') {
                        return form.querySelector(`[name="${field.name}"]:checked`);
                    }
                    if (field.type === 'checkbox') {
                        return field.checked;
                    }
                    return field.value.trim() !== '';
                });

                const progress = requiredFields.length > 0 ?
                    (filledFields.length / requiredFields.length) * 100 :
                    0;

                progressBarFill.style.width = progress + '%';

                // Update step indicators
                const sections = document.querySelectorAll('.khezana-form-section');
                sections.forEach((section, index) => {
                    const step = progressSteps[index];
                    if (!step) return;

                    const sectionFields = section.querySelectorAll('[required]');
                    const sectionFilled = Array.from(sectionFields).filter(field => {
                        if (field.type === 'radio') {
                            return form.querySelector(`[name="${field.name}"]:checked`);
                        }
                        return field.value.trim() !== '';
                    });

                    const sectionProgress = sectionFields.length > 0 ?
                        sectionFilled.length / sectionFields.length :
                        0;

                    if (sectionProgress === 1) {
                        step.classList.add('khezana-form-progress-step--completed');
                    } else if (sectionProgress > 0) {
                        step.classList.add('khezana-form-progress-step--in-progress');
                        step.classList.remove('khezana-form-progress-step--completed');
                    } else {
                        step.classList.remove('khezana-form-progress-step--completed',
                            'khezana-form-progress-step--in-progress');
                    }
                });
            }

            if (form) {
                form.addEventListener('input', updateProgress);
                form.addEventListener('change', updateProgress);
                updateProgress(); // Initial update
            }
        })();

        // Phase 4.1: Draft auto-save to localStorage
        (function() {
            const DRAFT_KEY = 'item_draft';
            const DEBOUNCE_MS = 2000;
            let debounceTimer;

            function buildDraft(form) {
                const d = {};
                const skip = ['_token', 'images'];
                for (const el of form.elements) {
                    if (skip.includes(el.name) || !el.name) continue;
                    if (el.type === 'file') continue;
                    if (el.type === 'radio' || el.type === 'checkbox') {
                        if (el.checked) d[el.name] = el.value;
                    } else {
                        d[el.name] = el.value;
                    }
                }
                return d;
            }

            function saveDraft() {
                const form = document.getElementById('itemCreateForm');
                if (!form) return;
                const draft = buildDraft(form);
                if (Object.keys(draft).length === 0) return;
                try {
                    localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
                    const ind = document.getElementById('draftSavedIndicator');
                    if (ind) {
                        ind.style.display = 'block';
                        setTimeout(function() {
                            ind.style.display = 'none';
                        }, 2000);
                    }
                } catch (e) {}
            }

            function restoreDraft() {
                const form = document.getElementById('itemCreateForm');
                if (!form) return;
                try {
                    const raw = localStorage.getItem(DRAFT_KEY);
                    if (!raw) return;
                    const draft = JSON.parse(raw);
                    if (!draft.category_id) return;

                    const cat = document.getElementById('category_id');
                    if (cat) {
                        cat.value = draft.category_id;
                        loadCategoryAttributes(draft.category_id);
                    }

                    const rest = () => {
                        for (const [name, value] of Object.entries(draft)) {
                            if (name === 'category_id') continue;
                            const els = form.querySelectorAll(`[name="${name}"]`);
                            if (!els.length) continue;
                            if (els[0].type === 'radio' || els[0].type === 'checkbox') {
                                els.forEach(function(el) {
                                    el.checked = (el.value === value);
                                });
                            } else {
                                els[0].value = value || '';
                            }
                        }
                        if (typeof toggleDepositAmountField === 'function') toggleDepositAmountField();
                    };
                    setTimeout(rest, 100);
                } catch (e) {}
            }

            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('itemCreateForm');
                if (!form) return;

                form.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(saveDraft, DEBOUNCE_MS);
                });
                form.addEventListener('change', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(saveDraft, DEBOUNCE_MS);
                });
                form.addEventListener('submit', function() {
                    try {
                        localStorage.removeItem(DRAFT_KEY);
                    } catch (e) {}
                });

                @if (!old('category_id'))
                    restoreDraft();
                @endif
            });
        })();
    </script>
@endsection
