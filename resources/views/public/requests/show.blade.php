@extends('layouts.app')

@section('title', $request->title . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-item-detail-page">
        <div class="khezana-container">
            @if(session('success'))
                <x-alert type="success" :message="session('success')" dismissible="true" class="khezana-mb-md" />
            @endif
            @if(session('error'))
                <x-alert type="error" :message="session('error')" dismissible="true" class="khezana-mb-md" />
            @endif

            <x-breadcrumb :items="[
                ['label' => __('requests.title'), 'url' => route('public.requests.index')],
                ['label' => $request->title, 'url' => null]
            ]" />

            <div class="khezana-item-detail-layout">
                <!-- Main Content -->
                <div class="khezana-item-details" style="max-width: 100%;">
                    <!-- Header -->
                    <div class="khezana-item-header">
                        <h1 class="khezana-item-detail-title">{{ $request->title }}</h1>
                        <div style="display: flex; gap: var(--khezana-spacing-sm); flex-wrap: wrap;">
                            <span class="khezana-request-badge khezana-request-badge-{{ $request->status }}">
                                {{ $request->statusLabel }}
                            </span>
                            @if ($request->offersCount > 0)
                                <span class="khezana-item-badge" style="background: var(--khezana-primary); color: white;">
                                    {{ $request->offersCount }} {{ __('common.ui.offers') }}
                                </span>
                            @endif
                        </div>
                        @if ($request->status == 'open')
                            <p class="khezana-status-explanation">
                                {{ __('requests.detail.status_explanation_open') }}
                            </p>
                        @elseif($request->status == 'fulfilled')
                            <p class="khezana-status-explanation">
                                {{ __('requests.detail.status_explanation_fulfilled') }}
                            </p>
                        @else
                            <p class="khezana-status-explanation">
                                {{ __('requests.detail.status_explanation_closed') }}
                            </p>
                        @endif
                    </div>

                    <!-- Category -->
                    @if ($request->category)
                        <div class="khezana-item-meta">
                            <span class="khezana-meta-label">{{ __('common.ui.category') }}:</span>
                            <span class="khezana-meta-value">{{ $request->category->name }}</span>
                        </div>
                    @endif

                    <!-- Attributes -->
                    @if ($request->attributes->count() > 0)
                        <div class="khezana-item-attributes">
                            <h3 class="khezana-section-title-small">{{ __('common.ui.specifications') }}</h3>
                            <div class="khezana-attributes-grid">
                                @foreach ($request->attributes as $attribute)
                                    <div class="khezana-attribute-item">
                                        <span class="khezana-attribute-name">{{ translate_attribute_name($attribute->name ?? '') }}:</span>
                                        <span class="khezana-attribute-value">{{ $attribute->value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Description -->
                    @if ($request->description)
                        <div class="khezana-item-description">
                            <h3 class="khezana-section-title-small">{{ __('common.ui.description') }}</h3>
                            <p class="khezana-description-text">{{ $request->description }}</p>
                        </div>
                    @endif

                    @php
                        $isOwner = auth()->check() && $request->user && $request->user->id === auth()->id();
                    @endphp

                    <!-- Next Steps Guide (owner only) -->
                    @if ($isOwner)
                    <div class="khezana-next-steps">
                        <h3 class="khezana-section-title-small">{{ __('requests.detail.next_steps_title') }}</h3>
                        <div class="khezana-next-steps-content">
                            @if ($request->status == 'open')
                                <div class="khezana-next-step-item">
                                    <div class="khezana-next-step-icon">✅</div>
                                    <div class="khezana-next-step-text">
                                        <p class="khezana-next-step-main">{{ __('requests.detail.next_steps_open') }}</p>
                                        <p class="khezana-next-step-hint">{{ __('requests.detail.next_steps_open_hint') }}</p>
                                    </div>
                                </div>
                                @if ($request->offersCount == 0)
                                    <div class="khezana-next-step-item">
                                        <div class="khezana-next-step-icon">⏳</div>
                                        <div class="khezana-next-step-text">
                                            <p class="khezana-next-step-main">{{ __('requests.detail.offers_coming') }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="khezana-next-step-item">
                                        <div class="khezana-next-step-icon">📋</div>
                                        <div class="khezana-next-step-text">
                                            <p class="khezana-next-step-main">{{ __('requests.detail.review_offers') }}</p>
                                        </div>
                                    </div>
                                    <div class="khezana-next-step-item">
                                        <div class="khezana-next-step-icon">💬</div>
                                        <div class="khezana-next-step-text">
                                            <p class="khezana-next-step-main">{{ __('requests.detail.contact_offerer') }}</p>
                                        </div>
                                    </div>
                                @endif
                            @elseif($request->status == 'fulfilled')
                                <div class="khezana-next-step-item">
                                    <div class="khezana-next-step-icon">🎉</div>
                                    <div class="khezana-next-step-text">
                                        <p class="khezana-next-step-main">{{ __('requests.detail.next_steps_fulfilled') }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="khezana-next-step-item">
                                    <div class="khezana-next-step-icon">🔒</div>
                                    <div class="khezana-next-step-text">
                                        <p class="khezana-next-step-main">{{ __('requests.detail.next_steps_closed') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Offers Section -->
                    @if ($request->offers->count() > 0)
                        <div class="khezana-offers-section">
                            <h3 class="khezana-section-title-small">
                                {{ __('common.ui.offers') }} ({{ $request->offersCount }})
                            </h3>
                            <div class="khezana-offers-list">
                                @foreach ($request->offers as $offer)
                                    <div class="khezana-offer-card">
                                        <div class="khezana-offer-header">
                                            <div>
                                                <span
                                                    class="khezana-item-badge khezana-item-badge-{{ $offer->operationType }}">
                                                    {{ __('items.operation_types.' . $offer->operationType) }}
                                                </span>
                                                @if ($offer->price && $offer->operationType != 'donate')
                                                    <span class="khezana-offer-price">
                                                        {{ number_format($offer->price, 0) }}
                                                        {{ __('common.ui.currency') }}
                                                        @if ($offer->operationType == 'rent')
                                                            <span
                                                                class="khezana-price-unit">{{ __('common.ui.per_day') }}</span>
                                                        @endif
                                                    </span>
                                                @elseif($offer->operationType == 'donate')
                                                    <span class="khezana-offer-price">{{ __('common.ui.free') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($offer->message)
                                            <p class="khezana-offer-message">{{ $offer->message }}</p>
                                        @endif
                                        @if ($offer->item)
                                            <div class="khezana-offer-item-link">
                                                <a href="{{ $offer->item->url }}" class="khezana-link">
                                                    {{ __('common.ui.view_item') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- CTA Button (hidden for request owner) -->
                    @if (! $isOwner)
                    <div class="khezana-item-cta">
                        @auth
                            <button type="button" onclick="showOfferForm()"
                                class="khezana-btn khezana-btn-primary khezana-btn-large khezana-btn-full">
                                {{ __('common.ui.submit_offer') }}
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                                class="khezana-btn khezana-btn-primary khezana-btn-large khezana-btn-full">
                                {{ __('common.ui.register_to_continue') }}
                            </a>
                        @endauth
                    </div>
                    @endif

                    <!-- Offer Form (Hidden by default; only for non-owners) - Full create-item style, send via WhatsApp/Telegram -->
                    @auth
                        @if (! $isOwner)
                        @php
                            $whatsappNumber = config('services.whatsapp.number', '+963959378002');
                            $telegramUsername = config('services.telegram.username', 'KARMO_VSKY');
                            $telegramContact = config('services.telegram.contact', '+963959378002');
                        @endphp
                        <div id="offerFormWrap" style="display: none; margin-top: var(--khezana-spacing-xl);">
                            {{-- Pre-Creation Notice Modal --}}
                            <div id="offerPreCreationNotice" class="khezana-notice-overlay" role="dialog" aria-modal="true"
                                aria-labelledby="offerPreCreationNoticeTitle" aria-describedby="offerPreCreationNoticeDesc">
                                <div class="khezana-notice-modal">
                                    <h2 id="offerPreCreationNoticeTitle" class="khezana-notice-title">
                                        {{ __('requests.detail.offer_form.pre_creation_notice.title') }}
                                    </h2>
                                    <ul id="offerPreCreationNoticeDesc" class="khezana-notice-list">
                                        @foreach ($preCreationRules ?? [] as $rule)
                                            <li class="khezana-notice-item">
                                                <span class="khezana-notice-icon" aria-hidden="true">{{ $rule['icon'] }}</span>
                                                <span>{{ $rule['text'] }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="khezana-notice-actions">
                                        <button type="button" id="offerNoticeCancel" class="khezana-btn khezana-btn-secondary" onclick="hideOfferForm()">
                                            {{ __('requests.detail.offer_form.pre_creation_notice.btn_cancel') }}
                                        </button>
                                        <button type="button" id="offerNoticeContinue" class="khezana-btn khezana-btn-primary khezana-btn-large">
                                            {{ __('requests.detail.offer_form.pre_creation_notice.btn_continue') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="offerForm" class="khezana-container khezana-create-form-wrap" hidden>
                                <div class="khezana-page-header">
                                    <h1 class="khezana-page-title">{{ __('requests.detail.offer_form.title') }}</h1>
                                    <p class="khezana-page-subtitle">{{ __('requests.detail.offer_form.subtitle') }}</p>
                                </div>

                                <div class="khezana-form-container">
                                    {{-- Progress Indicator --}}
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

                                    <form id="offerFormEl" class="khezana-form" enctype="multipart/form-data"
                                        data-request-id="{{ $request->id }}"
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
                                        data-free="{{ __('common.ui.free') }}">

                                        {{-- Section 1: Basic Information --}}
                                        <div class="khezana-form-section">
                                            <div class="khezana-form-section-header">
                                                <h2 class="khezana-form-section-title">
                                                    <span class="khezana-form-section-icon">👕</span>
                                                    {{ __('requests.detail.offer_form.sections.basic_info') }}
                                                </h2>
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
                                                        'categories' => $categories ?? collect(),
                                                        'name' => 'category_id',
                                                        'id' => 'offer_category_id',
                                                        'selected' => $request->category->id ?? null,
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
                                                </div>

                                                <div class="khezana-form-group">
                                                    <label for="offer_description" class="khezana-form-label">
                                                        <span class="khezana-form-label-icon">📄</span>
                                                        {{ __('items.fields.description') }}
                                                    </label>
                                                    <p class="khezana-form-hint">{{ __('requests.detail.offer_form.hints.description') }}</p>
                                                    <textarea name="description" id="offer_description" class="khezana-form-input khezana-form-textarea khezana-form-textarea--enhanced" rows="5"
                                                        placeholder="{{ __('items.placeholders.description') }}"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Section 2: Details --}}
                                        <div class="khezana-form-section">
                                            <div class="khezana-form-section-header">
                                                <h2 class="khezana-form-section-title">
                                                    <span class="khezana-form-section-icon">🔍</span>
                                                    {{ __('requests.detail.offer_form.sections.details') }}
                                                </h2>
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
                                                            <input type="radio" name="condition" value="new" required checked>
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
                                                    <div class="khezana-form-group" id="offerPriceGroup" style="display: block;">
                                                        <label for="offerPrice" class="khezana-form-label">
                                                            <span class="khezana-form-label-icon">💰</span>
                                                            {{ __('items.fields.price') }}
                                                        </label>
                                                        <p class="khezana-form-hint">{{ __('requests.detail.offer_form.hints.price') }}</p>
                                                        <div class="khezana-form-input-wrapper">
                                                            <input type="number" id="offerPrice" name="price" class="khezana-form-input khezana-form-input--enhanced"
                                                                step="0.01" min="0" placeholder="{{ __('items.placeholders.price') }}">
                                                            <span class="khezana-form-input-suffix">{{ __('common.ui.currency') }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="khezana-form-group" id="offerDepositGroup" style="display: none;">
                                                        <label for="offerDeposit" class="khezana-form-label">
                                                            <span class="khezana-form-label-icon">💳</span>
                                                            {{ __('items.fields.deposit_amount') }}
                                                        </label>
                                                        <p class="khezana-form-hint">{{ __('requests.detail.offer_form.hints.deposit_amount') }}</p>
                                                        <div class="khezana-form-input-wrapper">
                                                            <input type="number" id="offerDeposit" name="deposit_amount" class="khezana-form-input khezana-form-input--enhanced"
                                                                step="0.01" min="0" placeholder="{{ __('items.placeholders.deposit_amount') }}">
                                                            <span class="khezana-form-input-suffix">{{ __('common.ui.currency') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Section 3: Attributes --}}
                                        <div id="offerAttributesContainer" class="khezana-form-section" style="display: none;">
                                            <div class="khezana-form-section-header">
                                                <h2 class="khezana-form-section-title">
                                                    <span class="khezana-form-section-icon">🎨</span>
                                                    {{ __('items.fields.attributes') }}
                                                </h2>
                                                <p class="khezana-form-section-description">{{ __('requests.detail.offer_form.sections.attributes_desc') }}</p>
                                            </div>
                                            <div class="khezana-form-section-content">
                                                <div id="offerAttributesFields"></div>
                                            </div>
                                        </div>

                                        {{-- Section 4: Images --}}
                                        <div class="khezana-form-section">
                                            <div class="khezana-form-section-header">
                                                <h2 class="khezana-form-section-title">
                                                    <span class="khezana-form-section-icon">📷</span>
                                                    {{ __('items.fields.images') }}
                                                </h2>
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
                                                        <input type="file" name="images[]" id="offerImages" class="khezana-form-input"
                                                            accept="image/jpeg,image/jpg,image/png" capture="environment" multiple>
                                                    </div>
                                                    {{-- Image Preview Container --}}
                                                    <div id="offerImagePreviewContainer" class="khezana-image-preview-container" style="display: none;">
                                                        <div id="offerImagePreviewGrid" class="khezana-image-preview-grid"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="khezana-info-box">
                                            <div class="khezana-info-icon">ℹ️</div>
                                            <div class="khezana-info-content">
                                                <p class="khezana-info-text">
                                                    {{ __('requests.detail.offer_form.review_notice') }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="khezana-form-actions">
                                            <button type="button" id="offerPrepareBtn" class="khezana-btn khezana-btn-primary khezana-btn-large">
                                                {{ __('requests.detail.offer_form.prepare_offer') }}
                                            </button>
                                            <button type="button" onclick="hideOfferForm()" class="khezana-btn khezana-btn-secondary">
                                                {{ __('common.actions.cancel') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div id="offerShareBox" class="khezana-item-cta" style="display: none; margin-top: var(--khezana-spacing-xl);">
                                <h3 class="khezana-section-title-small" style="margin-bottom: var(--khezana-spacing-md);">
                                    {{ __('requests.detail.offer_form.send_via') }}
                                </h3>
                                <a id="offerWhatsAppBtn" href="#" target="_blank" rel="noopener noreferrer"
                                    class="khezana-btn khezana-btn-whatsapp khezana-btn-large khezana-btn-full">
                                    <svg class="khezana-btn-whatsapp__icon" fill="currentColor" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    <span>{{ __('common.ui.send_offer_whatsapp') }}</span>
                                </a>
                                <a id="offerTelegramBtn" href="#" target="_blank" rel="noopener noreferrer"
                                    class="khezana-btn khezana-btn-telegram khezana-btn-large khezana-btn-full">
                                    <svg class="khezana-btn-telegram__icon" fill="currentColor" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                    </svg>
                                    <span>{{ __('common.ui.send_offer_telegram') }}</span>
                                </a>
                                <p class="khezana-item-cta__contact-info">
                                    {{ __('common.ui.telegram_contact_info', ['number' => $telegramContact]) }}
                                </p>
                            </div>
                        </div>
                        @endif
                    @endauth

                    <!-- Additional Info -->
                    <div class="khezana-item-additional-info">
                        <div class="khezana-info-item">
                            <span class="khezana-info-icon">📅</span>
                            <span class="khezana-info-text">{{ __('common.ui.published') }}
                                {{ $request->createdAtFormatted }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @auth
        @if (! $isOwner)
    <script>
        // Translation map for common attribute names
        const offerAttributeTranslations = {
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

        function translateOfferAttributeName(name) {
            const lowerName = name.toLowerCase().trim();
            return offerAttributeTranslations[lowerName] || name;
        }

        function showOfferForm() {
            const wrap = document.getElementById('offerFormWrap');
            const modal = document.getElementById('offerPreCreationNotice');
            if (!wrap || !modal) return;
            wrap.style.display = 'block';
            modal.style.display = 'flex';
            wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            const shareBox = document.getElementById('offerShareBox');
            if (shareBox) shareBox.style.display = 'none';
        }

        function hideOfferForm() {
            const wrap = document.getElementById('offerFormWrap');
            if (wrap) wrap.style.display = 'none';
            const modal = document.getElementById('offerPreCreationNotice');
            if (modal) modal.style.display = 'none';
            const form = document.getElementById('offerForm');
            if (form) form.hidden = true;
        }

        function loadOfferCategoryAttributes(categoryId) {
            const select = document.getElementById('offer_category_id');
            const selectedOption = select ? select.options[select.selectedIndex] : null;
            const attributesContainer = document.getElementById('offerAttributesContainer');
            const attributesFields = document.getElementById('offerAttributesFields');

            if (!categoryId || !selectedOption) {
                if (attributesContainer) attributesContainer.style.display = 'none';
                if (attributesFields) attributesFields.innerHTML = '';
                return;
            }

            try {
                const attributes = JSON.parse(selectedOption.getAttribute('data-attributes') || '[]');

                if (attributes.length === 0) {
                    if (attributesContainer) attributesContainer.style.display = 'none';
                    if (attributesFields) attributesFields.innerHTML = '';
                    return;
                }

                if (attributesContainer) attributesContainer.style.display = 'block';
                if (attributesFields) attributesFields.innerHTML = '';

                attributes.forEach(attribute => {
                    const attributeDiv = document.createElement('div');
                    attributeDiv.className = 'khezana-form-group';

                    const label = document.createElement('label');
                    label.className = 'khezana-form-label';
                    label.htmlFor = `offer_attributes[${attribute.slug}]`;
                    label.textContent = translateOfferAttributeName(attribute.name);
                    if (attribute.is_required) {
                        label.innerHTML += ' <span class="khezana-required">*</span>';
                    }

                    let input;

                    if (attribute.type === 'select') {
                        input = document.createElement('select');
                        input.className = 'khezana-form-input khezana-form-select';
                        input.name = `attributes[${attribute.slug}]`;
                        input.id = `offer_attributes[${attribute.slug}]`;
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
                        input.id = `offer_attributes[${attribute.slug}]`;
                        if (attribute.is_required) {
                            input.required = true;
                        }
                    } else {
                        input = document.createElement('input');
                        input.type = 'text';
                        input.className = 'khezana-form-input';
                        input.name = `attributes[${attribute.slug}]`;
                        input.id = `offer_attributes[${attribute.slug}]`;
                        if (attribute.is_required) {
                            input.required = true;
                        }

                        if (attribute.slug === 'size') {
                            input.placeholder = '{{ __('attributes.placeholders.size') }}';
                        }
                    }

                    attributeDiv.appendChild(label);
                    attributeDiv.appendChild(input);

                    if (attribute.slug === 'size' && attribute.type === 'text') {
                        const helper = document.createElement('p');
                        helper.className = 'khezana-form-hint';
                        helper.textContent = '{{ __('attributes.helpers.size') }}';
                        attributeDiv.appendChild(helper);
                    }

                    if (attributesFields) attributesFields.appendChild(attributeDiv);
                });
            } catch (e) {
                console.error('Error loading attributes:', e);
                if (attributesContainer) attributesContainer.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('offerFormEl');
            const formWrap = document.getElementById('offerFormWrap');
            const modal = document.getElementById('offerPreCreationNotice');
            const noticeContinue = document.getElementById('offerNoticeContinue');
            const noticeCancel = document.getElementById('offerNoticeCancel');
            const offerForm = document.getElementById('offerForm');
            const priceGroup = document.getElementById('offerPriceGroup');
            const depositGroup = document.getElementById('offerDepositGroup');
            const prepareBtn = document.getElementById('offerPrepareBtn');
            const shareBox = document.getElementById('offerShareBox');
            const waBtn = document.getElementById('offerWhatsAppBtn');
            const tgBtn = document.getElementById('offerTelegramBtn');
            const progressBarFill = document.getElementById('offerProgressBarFill');
            const categorySelect = document.getElementById('offer_category_id');

            if (!form || !prepareBtn) return;

            // Pre-creation notice modal
            if (noticeContinue && modal && offerForm) {
                noticeContinue.addEventListener('click', function() {
                    modal.style.display = 'none';
                    offerForm.hidden = false;
                    offerForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
            }

            // Category change - load attributes
            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    loadOfferCategoryAttributes(this.value);
                    updateProgressBar();
                });
                // Load initial if category is pre-selected
                if (categorySelect.value) {
                    setTimeout(() => loadOfferCategoryAttributes(categorySelect.value), 100);
                }
            }

            // Toggle price/deposit based on operation type
            function togglePriceDeposit() {
                const op = form.querySelector('input[name="operation_type"]:checked');
                if (!op || !priceGroup || !depositGroup) return;
                if (op.value === 'donate') {
                    priceGroup.style.display = 'none';
                    depositGroup.style.display = 'none';
                } else if (op.value === 'rent') {
                    priceGroup.style.display = 'block';
                    depositGroup.style.display = 'block';
                } else {
                    priceGroup.style.display = 'block';
                    depositGroup.style.display = 'none';
                }
            }

            form.querySelectorAll('input[name="operation_type"]').forEach(function(radio) {
                radio.addEventListener('change', togglePriceDeposit);
            });
            togglePriceDeposit();

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
                        if (!file.type.match('image.*') || file.size > 5 * 1024 * 1024) return;

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
                        if (i !== index) dt.items.add(file);
                    });
                    imageInput.files = dt.files;
                    updatePreview();
                }

                document.addEventListener('DOMContentLoaded', function() {
                    imageInput = document.getElementById('offerImages');
                    previewContainer = document.getElementById('offerImagePreviewContainer');
                    previewGrid = document.getElementById('offerImagePreviewGrid');
                    const dropZone = document.getElementById('offerImageDropZone');

                    if (!imageInput || !previewContainer || !previewGrid) return;

                    imageInput.addEventListener('change', updatePreview);

                    if (dropZone) {
                        dropZone.addEventListener('click', () => imageInput.click());
                        dropZone.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            dropZone.classList.add('khezana-image-drop-zone--dragover');
                        });
                        dropZone.addEventListener('dragleave', () => {
                            dropZone.classList.remove('khezana-image-drop-zone--dragover');
                        });
                        dropZone.addEventListener('drop', (e) => {
                            e.preventDefault();
                            dropZone.classList.remove('khezana-image-drop-zone--dragover');
                            if (e.dataTransfer.files.length) {
                                const dt = new DataTransfer();
                                Array.from(imageInput.files).forEach(f => dt.items.add(f));
                                Array.from(e.dataTransfer.files).forEach(f => {
                                    if (f.type.match('image.*') && f.size <= 5 * 1024 * 1024) dt.items.add(f);
                                });
                                imageInput.files = dt.files;
                                updatePreview();
                            }
                        });
                    }
                });
            })();

            // Progress bar update
            function updateProgressBar() {
                if (!progressBarFill) return;
                const sections = form.querySelectorAll('.khezana-form-section');
                const visibleSections = Array.from(sections).filter(s => s.style.display !== 'none');
                const currentStep = Math.min(visibleSections.length, 3);
                const progress = (currentStep / 3) * 100;
                progressBarFill.style.width = progress + '%';
            }

            // Build message from all form fields
            function buildMessage() {
                const requestId = form.dataset.requestId || '';
                const op = form.querySelector('input[name="operation_type"]:checked');
                const opSell = form.dataset.opSell || 'sell';
                const opRent = form.dataset.opRent || 'rent';
                const opDonate = form.dataset.opDonate || 'donate';
                const currency = form.dataset.currency || '';
                const perDay = form.dataset.perDay || '';
                const free = form.dataset.free || 'free';

                let opLabel = opSell;
                if (op && op.value === 'rent') opLabel = opRent;
                else if (op && op.value === 'donate') opLabel = opDonate;

                const categorySelect = document.getElementById('offer_category_id');
                const categoryName = categorySelect && categorySelect.options[categorySelect.selectedIndex]
                    ? categorySelect.options[categorySelect.selectedIndex].text.trim().replace(/└─\s*/, '')
                    : '';
                const titleVal = (form.querySelector('[name="title"]')?.value || '').trim();
                const descVal = (form.querySelector('[name="description"]')?.value || '').trim();
                const condition = form.querySelector('input[name="condition"]:checked');
                const conditionLabel = condition ? (condition.value === 'new' ? '{{ __('items.conditions.new') }}' : '{{ __('items.conditions.used') }}') : '';
                const priceVal = (form.querySelector('[name="price"]')?.value || '').trim();
                const depositVal = (form.querySelector('[name="deposit_amount"]')?.value || '').trim();
                const imageInput = document.getElementById('offerImages');
                const hasImages = imageInput && imageInput.files && imageInput.files.length > 0;
                const imageCount = hasImages ? imageInput.files.length : 0;

                // Collect attributes
                const attributeInputs = form.querySelectorAll('[name^="attributes["]');
                const attributes = [];
                attributeInputs.forEach(input => {
                    if (input.value && input.value.trim()) {
                        const label = input.previousElementSibling?.textContent?.replace(/\s*\*?\s*$/, '') || input.name;
                        attributes.push(label + ': ' + input.value.trim());
                    }
                });

                // Build a friendly message
                let lines = [];
                lines.push('مرحباً 👋');
                lines.push('');
                lines.push('أود تقديم عرض على الطلب رقم #' + requestId);
                lines.push('');
                lines.push('📋 تفاصيل العرض:');
                lines.push('');

                if (titleVal) {
                    lines.push('📌 ' + titleVal);
                    lines.push('');
                }

                if (categoryName) {
                    lines.push('👗 الفئة: ' + categoryName);
                }

                if (op) {
                    lines.push('💼 نوع العملية: ' + opLabel);
                }

                if (descVal) {
                    lines.push('');
                    lines.push('📄 الوصف:');
                    lines.push(descVal);
                }

                if (conditionLabel) {
                    lines.push('');
                    lines.push('🏷️ الحالة: ' + conditionLabel);
                }

                if (op && op.value === 'sell' && priceVal) {
                    lines.push('💰 السعر: ' + priceVal + ' ' + currency);
                } else if (op && op.value === 'rent') {
                    if (priceVal) lines.push('💰 السعر: ' + priceVal + ' ' + currency + ' ' + perDay);
                    if (depositVal) lines.push('💳 مبلغ التأمين: ' + depositVal + ' ' + currency);
                } else if (op && op.value === 'donate') {
                    lines.push('🎁 السعر: ' + free);
                }

                if (attributes.length > 0) {
                    lines.push('');
                    lines.push('🎨 المواصفات:');
                    attributes.forEach(attr => lines.push('  • ' + attr));
                }

                if (hasImages) {
                    lines.push('');
                    lines.push('📷 الصور:');
                    lines.push('يوجد ' + imageCount + ' صورة لهذا العرض');
                    lines.push('(يرجى إرفاق الصور عند إرسال الرسالة)');
                }

                lines.push('');
                lines.push('شكراً لوقتك 🙏');
                lines.push('');

                return lines.join('\n');
            }

            function validateOffer() {
                const category = document.getElementById('offer_category_id');
                const op = form.querySelector('input[name="operation_type"]:checked');
                const title = form.querySelector('[name="title"]');
                const condition = form.querySelector('input[name="condition"]:checked');
                if (!category || !category.value) {
                    if (category) category.focus();
                    return false;
                }
                if (!op) return false;
                if (!title || !title.value.trim()) {
                    if (title) title.focus();
                    return false;
                }
                if (!condition) {
                    const conditionInputs = form.querySelectorAll('input[name="condition"]');
                    if (conditionInputs.length) conditionInputs[0].focus();
                    return false;
                }
                return true;
            }

            prepareBtn.addEventListener('click', function() {
                if (!validateOffer()) {
                    alert('يرجى ملء جميع الحقول المطلوبة');
                    return;
                }
                const text = buildMessage();
                const encoded = encodeURIComponent(text);
                const waNumber = (form.dataset.whatsappNumber || '').replace(/\D/g, '');
                const tgUsername = (form.dataset.telegramUsername || '').replace(/^@/, '');

                if (waBtn && waNumber) waBtn.href = 'https://wa.me/' + waNumber + '?text=' + encoded;
                if (tgBtn && tgUsername) tgBtn.href = 'https://t.me/' + tgUsername + '?text=' + encoded;

                if (shareBox) {
                    shareBox.style.display = 'block';
                    shareBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });

            // Update progress on scroll/section visibility
            const observer = new IntersectionObserver((entries) => {
                updateProgressBar();
            }, { threshold: 0.5 });

            form.querySelectorAll('.khezana-form-section').forEach(section => {
                observer.observe(section);
            });
            updateProgressBar();
        });
    </script>
        @endif
    @endauth
@endsection
