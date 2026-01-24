{{-- Offer Form: create-item style, send via WhatsApp/Telegram. Expects $request, $categories, $preCreationRules. --}}
@php
    $categories = $categories ?? collect();
    $preCreationRules = $preCreationRules ?? [];
    $whatsappNumber = config('services.whatsapp.number', '+963959378002');
    $telegramUsername = config('services.telegram.username', 'KARMO_VSKY');
    $telegramContact = config('services.telegram.contact', '+963959378002');
    $requestCategoryId = $request->category->id ?? null;
@endphp

{{-- Pre-creation notice modal (like create item) --}}
<div id="offerPreNotice" class="khezana-notice-overlay" role="dialog" aria-modal="true" aria-labelledby="offerPreNoticeTitle" aria-describedby="offerPreNoticeDesc" style="display: none;">
    <div class="khezana-notice-modal">
        <h2 id="offerPreNoticeTitle" class="khezana-notice-title">{{ __('requests.detail.offer_form.pre_creation_notice.title') }}</h2>
        <ul id="offerPreNoticeDesc" class="khezana-notice-list">
            @foreach ($preCreationRules as $rule)
                <li class="khezana-notice-item">
                    <span class="khezana-notice-icon" aria-hidden="true">{{ $rule['icon'] }}</span>
                    <span>{{ $rule['text'] }}</span>
                </li>
            @endforeach
        </ul>
        <div class="khezana-notice-actions">
            <button type="button" id="offerNoticeCancel" class="khezana-btn khezana-btn-secondary">{{ __('requests.detail.offer_form.pre_creation_notice.btn_cancel') }}</button>
            <button type="button" id="offerNoticeContinue" class="khezana-btn khezana-btn-primary khezana-btn-large">{{ __('requests.detail.offer_form.pre_creation_notice.btn_continue') }}</button>
        </div>
    </div>
</div>

<div id="offerForm" class="khezana-form-container" style="display: none; margin-top: var(--khezana-spacing-xl);">
    <div class="khezana-page-header" style="margin-bottom: var(--khezana-spacing-lg);">
        <h2 class="khezana-page-title">{{ __('requests.detail.offer_form.title') }}</h2>
        <p class="khezana-page-subtitle">{{ __('requests.detail.offer_form.subtitle') }}</p>
    </div>

    <div class="khezana-form-progress">
        <div class="khezana-form-progress-steps">
            <div class="khezana-form-progress-step" data-step="1">
                <div class="khezana-form-progress-step-number">1</div>
                <div class="khezana-form-progress-step-label">{{ __('requests.detail.offer_form.sections.basic_info') }}</div>
            </div>
            <div class="khezana-form-progress-step" data-step="2">
                <div class="khezana-form-progress-step-number">2</div>
                <div class="khezana-form-progress-step-label">{{ __('requests.detail.offer_form.sections.details') }}</div>
            </div>
            <div class="khezana-form-progress-step" data-step="3">
                <div class="khezana-form-progress-step-number">3</div>
                <div class="khezana-form-progress-step-label">{{ __('items.fields.images') }}</div>
            </div>
        </div>
        <div class="khezana-form-progress-bar">
            <div class="khezana-form-progress-bar-fill" id="offerProgressBarFill"></div>
        </div>
    </div>

    <form id="offerFormEl" class="khezana-form" data-request-id="{{ $request->id }}"
        data-request-url="{{ route('public.requests.show', ['id' => $request->id, 'slug' => $request->slug]) }}"
        data-whatsapp-number="{{ $whatsappNumber }}"
        data-telegram-username="{{ $telegramUsername }}"
        data-telegram-contact="{{ $telegramContact }}"
        data-msg-intro="{{ __('requests.detail.offer_form.message_intro', ['id' => $request->id]) }}"
        data-op-sell="{{ __('items.operation_types.sell') }}"
        data-op-rent="{{ __('items.operation_types.rent') }}"
        data-op-donate="{{ __('items.operation_types.donate') }}"
        data-currency="{{ __('common.ui.currency') }}"
        data-per-day="{{ __('common.ui.per_day') }}"
        data-free="{{ __('common.ui.free') }}"
        data-label-operation="{{ __('items.fields.operation_type') }}"
        data-label-price="{{ __('items.fields.price') }}"
        data-label-deposit="{{ __('items.fields.deposit_amount') }}"
        data-label-title="{{ __('items.fields.title') }}"
        data-label-description="{{ __('items.fields.description') }}"
        data-label-condition="{{ __('items.fields.condition') }}"
        data-label-category="{{ __('items.fields.category') }}"
        data-label-attributes="{{ __('items.fields.attributes') }}"
        data-label-images="{{ __('items.fields.images') }}">

        {{-- Section 1: Basic Information --}}
        <div class="khezana-form-section">
            <div class="khezana-form-section-header">
                <h3 class="khezana-form-section-title">
                    <span class="khezana-form-section-icon">👕</span>
                    {{ __('requests.detail.offer_form.sections.basic_info') }}
                </h3>
                <p class="khezana-form-section-description">{{ __('requests.detail.offer_form.sections.basic_info_desc') }}</p>
            </div>
            <div class="khezana-form-section-content">
                <div class="khezana-form-group">
                    <label for="offer_category_id" class="khezana-form-label">
                        <span class="khezana-form-label-icon">👗</span>
                        {{ __('items.fields.category') }}
                        <span class="khezana-required">*</span>
                    </label>
                    @include('components.category-select', [
                        'categories' => $categories,
                        'name' => 'category_id',
                        'id' => 'offer_category_id',
                        'selected' => $requestCategoryId,
                        'required' => true,
                        'placeholder' => __('common.ui.select_category'),
                        'showAllOption' => false,
                        'attributes' => true,
                        'onchange' => 'loadOfferCategoryAttributes(this.value)',
                    ])
                    <p class="khezana-form-hint">{{ __('requests.detail.offer_form.hints.category') }}</p>
                </div>

                <div class="khezana-form-group">
                    <label class="khezana-form-label">
                        <span class="khezana-form-label-icon">💼</span>
                        {{ __('items.fields.operation_type') }}
                        <span class="khezana-required">*</span>
                    </label>
                    <p class="khezana-form-hint">{{ __('requests.detail.offer_form.hints.operation_type') }}</p>
                    <div class="khezana-filter-options khezana-filter-options--enhanced">
                        <label class="khezana-filter-option khezana-filter-option--card">
                            <input type="radio" name="operation_type" value="sell" required checked>
                            <div class="khezana-filter-option-content">
                                <span class="khezana-filter-option-icon">💵</span>
                                <span class="khezana-filter-option-label">{{ __('items.operation_types.sell') }}</span>
                            </div>
                        </label>
                        <label class="khezana-filter-option khezana-filter-option--card">
                            <input type="radio" name="operation_type" value="rent" required>
                            <div class="khezana-filter-option-content">
                                <span class="khezana-filter-option-icon">👔</span>
                                <span class="khezana-filter-option-label">{{ __('items.operation_types.rent') }}</span>
                            </div>
                        </label>
                        <label class="khezana-filter-option khezana-filter-option--card">
                            <input type="radio" name="operation_type" value="donate" required>
                            <div class="khezana-filter-option-content">
                                <span class="khezana-filter-option-icon">🎀</span>
                                <span class="khezana-filter-option-label">{{ __('items.operation_types.donate') }}</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="khezana-form-group">
                    <label for="offer_title" class="khezana-form-label">
                        <span class="khezana-form-label-icon">✏️</span>
                        {{ __('items.fields.title') }}
                        <span class="khezana-required">*</span>
                    </label>
                    <input type="text" name="title" id="offer_title" class="khezana-form-input khezana-form-input--enhanced"
                        placeholder="{{ __('items.placeholders.title') }}" required maxlength="255">
                    <p class="khezana-form-hint">{{ __('requests.detail.offer_form.hints.title') }}</p>
                </div>

                <div class="khezana-form-group">
                    <label for="offer_description" class="khezana-form-label">
                        <span class="khezana-form-label-icon">📄</span>
                        {{ __('items.fields.description') }}
                    </label>
                    <textarea name="description" id="offer_description" class="khezana-form-input khezana-form-textarea khezana-form-textarea--enhanced" rows="5"
                        placeholder="{{ __('items.placeholders.description') }}"></textarea>
                    <p class="khezana-form-hint">{{ __('requests.detail.offer_form.hints.description') }}</p>
                </div>
            </div>
        </div>

        {{-- Section 2: Details --}}
        <div class="khezana-form-section">
            <div class="khezana-form-section-header">
                <h3 class="khezana-form-section-title">
                    <span class="khezana-form-section-icon">🔍</span>
                    {{ __('requests.detail.offer_form.sections.details') }}
                </h3>
                <p class="khezana-form-section-description">{{ __('requests.detail.offer_form.sections.details_desc') }}</p>
            </div>
            <div class="khezana-form-section-content">
                <div class="khezana-form-group">
                    <label class="khezana-form-label">
                        <span class="khezana-form-label-icon">🏷️</span>
                        {{ __('items.fields.condition') }}
                        <span class="khezana-required">*</span>
                    </label>
                    <p class="khezana-form-hint">{{ __('requests.detail.offer_form.hints.condition') }}</p>
                    <div class="khezana-filter-options khezana-filter-options--enhanced">
                        <label class="khezana-filter-option khezana-filter-option--card">
                            <input type="radio" name="condition" value="new" required>
                            <div class="khezana-filter-option-content">
                                <span class="khezana-filter-option-icon">✨</span>
                                <div class="khezana-filter-option-text">
                                    <span class="khezana-filter-option-label">{{ __('items.conditions.new') }}</span>
                                    <small class="khezana-option-hint">{{ __('items.conditions.new_hint') }}</small>
                                </div>
                            </div>
                        </label>
                        <label class="khezana-filter-option khezana-filter-option--card">
                            <input type="radio" name="condition" value="used" required>
                            <div class="khezana-filter-option-content">
                                <span class="khezana-filter-option-icon">♻️</span>
                                <div class="khezana-filter-option-text">
                                    <span class="khezana-filter-option-label">{{ __('items.conditions.used') }}</span>
                                    <small class="khezana-option-hint">{{ __('items.conditions.used_hint') }}</small>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="khezana-form-row khezana-form-row--enhanced">
                    <div class="khezana-form-group">
                        <label for="offer_price" class="khezana-form-label">
                            <span class="khezana-form-label-icon">💰</span>
                            {{ __('items.fields.price') }}
                        </label>
                        <p class="khezana-form-hint">{{ __('requests.detail.offer_form.hints.price') }}</p>
                        <div class="khezana-form-input-wrapper">
                            <input type="number" step="0.01" name="price" id="offer_price" class="khezana-form-input khezana-form-input--enhanced"
                                placeholder="{{ __('items.placeholders.price') }}" min="0">
                            <span class="khezana-form-input-suffix">{{ __('common.ui.currency') }}</span>
                        </div>
                    </div>
                    <div class="khezana-form-group" id="offerDepositGroup" style="display: none;">
                        <label for="offer_deposit_amount" class="khezana-form-label">
                            <span class="khezana-form-label-icon">💳</span>
                            {{ __('items.fields.deposit_amount') }}
                        </label>
                        <p class="khezana-form-hint">{{ __('requests.detail.offer_form.hints.deposit_amount') }}</p>
                        <div class="khezana-form-input-wrapper">
                            <input type="number" step="0.01" name="deposit_amount" id="offer_deposit_amount" class="khezana-form-input khezana-form-input--enhanced"
                                placeholder="{{ __('items.placeholders.deposit_amount') }}" min="0">
                            <span class="khezana-form-input-suffix">{{ __('common.ui.currency') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Attributes --}}
        <div id="offerAttributesContainer" class="khezana-form-section" style="display: none;">
            <div class="khezana-form-section-header">
                <h3 class="khezana-form-section-title">
                    <span class="khezana-form-section-icon">🎨</span>
                    {{ __('items.fields.attributes') }}
                </h3>
                <p class="khezana-form-section-description">{{ __('requests.detail.offer_form.sections.attributes_desc') }}</p>
            </div>
            <div class="khezana-form-section-content">
                <div id="offerAttributesFields"></div>
            </div>
        </div>

        {{-- Section 4: Images --}}
        <div class="khezana-form-section">
            <div class="khezana-form-section-header">
                <h3 class="khezana-form-section-title">
                    <span class="khezana-form-section-icon">📷</span>
                    {{ __('items.fields.images') }}
                </h3>
                <p class="khezana-form-section-description">{{ __('requests.detail.offer_form.sections.images_desc') }}</p>
            </div>
            <div class="khezana-form-section-content">
                <div class="khezana-form-group">
                    <p class="khezana-form-hint">{{ __('requests.detail.offer_form.hints.images') }}</p>
                    <div id="offerImageDropZone" class="khezana-image-drop-zone khezana-image-drop-zone--enhanced" aria-label="{{ __('requests.detail.offer_form.hints.images') }}">
                        <div class="khezana-image-drop-zone-content">
                            <span class="khezana-image-drop-zone-icon">📸</span>
                            <span class="khezana-image-drop-zone__label">{{ __('requests.detail.offer_form.hints.drop_images') }}</span>
                            <span class="khezana-image-drop-zone-hint">{{ __('requests.detail.offer_form.hints.click_or_drag') }}</span>
                        </div>
                        <input type="file" name="images[]" id="offer_images" class="khezana-form-input" accept="image/jpeg,image/jpg,image/png" multiple>
                    </div>
                    <div id="offerImagePreviewContainer" class="khezana-image-preview-container" style="display: none;">
                        <div id="offerImagePreviewGrid" class="khezana-image-preview-grid"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="khezana-info-box">
            <div class="khezana-info-icon">ℹ️</div>
            <div class="khezana-info-content">
                <p class="khezana-info-text">{{ __('requests.detail.offer_form.review_notice') }}</p>
            </div>
        </div>

        <div class="khezana-form-actions" style="display: flex; gap: var(--khezana-spacing-md); flex-wrap: wrap;">
            <button type="button" id="offerPrepareBtn" class="khezana-btn khezana-btn-primary khezana-btn-large">
                {{ __('requests.detail.offer_form.prepare_offer') }}
            </button>
            <button type="button" onclick="hideOfferForm()" class="khezana-btn khezana-btn-secondary">
                {{ __('common.actions.cancel') }}
            </button>
        </div>
    </form>

    <div id="offerShareBox" class="khezana-item-cta" style="display: none; margin-top: var(--khezana-spacing-xl);">
        <h3 class="khezana-section-title-small" style="margin-bottom: var(--khezana-spacing-md);">
            {{ __('requests.detail.offer_form.send_via') }}
        </h3>
        <a id="offerWhatsAppBtn" href="#" target="_blank" rel="noopener noreferrer"
            class="khezana-btn khezana-btn-whatsapp khezana-btn-large khezana-btn-full">
            <svg class="khezana-btn-whatsapp__icon" fill="currentColor" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
            <span>{{ __('common.ui.order_now_whatsapp') }}</span>
        </a>
        <a id="offerTelegramBtn" href="#" target="_blank" rel="noopener noreferrer"
            class="khezana-btn khezana-btn-telegram khezana-btn-large khezana-btn-full">
            <svg class="khezana-btn-telegram__icon" fill="currentColor" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
            </svg>
            <span>{{ __('common.ui.order_now_telegram') }}</span>
        </a>
        <p class="khezana-item-cta__contact-info">
            {{ __('common.ui.telegram_contact_info', ['number' => $telegramContact]) }}
        </p>
        <p class="khezana-form-hint" style="margin-top: var(--khezana-spacing-sm);">
            {{ __('requests.detail.offer_form.link_request') }}: <a id="offerRequestLink" href="#" class="khezana-link" target="_blank" rel="noopener">{{ __('requests.detail.offer_form.open_link') }}</a>
        </p>
    </div>
</div>
