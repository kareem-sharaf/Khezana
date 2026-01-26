@php
    use App\Helpers\ItemCardHelper;
@endphp

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

                    <!-- Offer Form (Hidden by default; only for non-owners) -->
                    @auth
                        @if (! $isOwner)
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
                                        method="POST"
                                        action="{{ route('public.requests.offer', $request->id) }}"
                                        data-request-id="{{ $request->id }}">
                                        @csrf

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
                                                    <label class="khezana-form-label">
                                                        <span class="khezana-form-label-icon">👗</span>
                                                        {{ __('items.fields.category') }}
                                                    </label>
                                                    {{-- Hidden field with request's category --}}
                                                    <input type="hidden" name="category_id" value="{{ $request->category->id ?? '' }}">
                                                    {{-- Display category name (read-only) --}}
                                                    <input type="text"
                                                        class="khezana-form-input"
                                                        value="{{ $request->category->name ?? '' }}"
                                                        disabled
                                                        style="background-color: var(--khezana-bg-secondary); cursor: not-allowed;">
                                                    <p class="khezana-form-hint">{{ __('requests.detail.offer_form.hints.category_locked') }}</p>
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
                                            <button type="submit" id="offerSubmitBtn" class="khezana-btn khezana-btn-primary khezana-btn-large">
                                                {{ __('requests.detail.offer_form.submit_offer') }}
                                            </button>
                                            <button type="button" onclick="hideOfferForm()" class="khezana-btn khezana-btn-secondary">
                                                {{ __('common.actions.cancel') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
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

            <!-- Suggested Items Section -->
            @if ($suggestedItems && $suggestedItems->count() > 0)
                <section class="khezana-similar-items" aria-label="{{ __('requests.detail.suggested_items') }}">
                    <div class="khezana-similar-items__header">
                        <h2 class="khezana-similar-items__title">
                            {{ __('requests.detail.suggested_items') }}
                        </h2>
                        <p class="khezana-similar-items__subtitle">
                            {{ __('requests.detail.suggested_items_description') }}
                        </p>
                    </div>

                    <div class="khezana-items-grid-modern" role="list">
                        @foreach ($suggestedItems as $item)
                            <div role="listitem">
                                @include(
                                    'partials.item-card',
                                    array_merge(['item' => $item], ItemCardHelper::preparePublicItem($item)))
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
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

        // Request attributes from the server (pre-filled values)
        const requestAttributes = @json($request->attributes->mapWithKeys(fn($attr) => [$attr->name => $attr->value]));

        // Category attributes definition (from controller)
        const categoryAttributes = @json($categoryAttributes ?? []);

        function loadOfferCategoryAttributes() {
            const attributesContainer = document.getElementById('offerAttributesContainer');
            const attributesFields = document.getElementById('offerAttributesFields');

            if (!categoryAttributes || categoryAttributes.length === 0) {
                if (attributesContainer) attributesContainer.style.display = 'none';
                if (attributesFields) attributesFields.innerHTML = '';
                return;
            }

            if (attributesContainer) attributesContainer.style.display = 'block';
            if (attributesFields) attributesFields.innerHTML = '';

            categoryAttributes.forEach(attribute => {
                const attributeDiv = document.createElement('div');
                attributeDiv.className = 'khezana-form-group';

                const label = document.createElement('label');
                label.className = 'khezana-form-label';
                label.htmlFor = `offer_attributes[${attribute.slug}]`;
                label.textContent = translateOfferAttributeName(attribute.name);
                if (attribute.is_required) {
                    label.innerHTML += ' <span class="khezana-required">*</span>';
                }

                // Get pre-filled value from request attributes
                const prefilledValue = requestAttributes[attribute.name] || '';

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
                            // Pre-select if matches request attribute
                            if (value === prefilledValue) {
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
                    input.id = `offer_attributes[${attribute.slug}]`;
                    input.value = prefilledValue;
                    if (attribute.is_required) {
                        input.required = true;
                    }
                } else {
                    input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'khezana-form-input';
                    input.name = `attributes[${attribute.slug}]`;
                    input.id = `offer_attributes[${attribute.slug}]`;
                    input.value = prefilledValue;
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
            const submitBtn = document.getElementById('offerSubmitBtn');
            const progressBarFill = document.getElementById('offerProgressBarFill');

            if (!form || !submitBtn) return;

            // Pre-creation notice modal
            if (noticeContinue && modal && offerForm) {
                noticeContinue.addEventListener('click', function() {
                    modal.style.display = 'none';
                    offerForm.hidden = false;
                    offerForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
            }

            // Load attributes automatically (category is locked to request's category)
            setTimeout(() => {
                loadOfferCategoryAttributes();
                updateProgressBar();
            }, 100);

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
                const imageInput = document.getElementById('offerImages');
                const previewContainer = document.getElementById('offerImagePreviewContainer');
                const previewGrid = document.getElementById('offerImagePreviewGrid');
                const dropZone = document.getElementById('offerImageDropZone');

                if (!imageInput || !previewContainer || !previewGrid) return;

                function updatePreview() {
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

                imageInput.addEventListener('change', updatePreview);

                if (dropZone) {
                    dropZone.addEventListener('click', (e) => {
                        // Prevent double trigger when clicking directly on the input
                        if (e.target !== imageInput) {
                            imageInput.click();
                        }
                    });
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

            function validateOffer() {
                const op = form.querySelector('input[name="operation_type"]:checked');
                const title = form.querySelector('[name="title"]');
                const condition = form.querySelector('input[name="condition"]:checked');
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

            // Handle form submission
            form.addEventListener('submit', function(e) {
                if (!validateOffer()) {
                    e.preventDefault();
                    alert('{{ __('requests.detail.offer_form.validation_error') }}');
                    return false;
                }

                // Disable submit button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="khezana-spinner"></span> {{ __('common.ui.submitting') }}';
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
