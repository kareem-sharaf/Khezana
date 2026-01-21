@extends('layouts.app')

@section('title', $request->title . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-item-detail-page">
        <div class="khezana-container">
            <!-- Breadcrumb -->
            <nav class="khezana-breadcrumb">
                <a href="{{ route('home') }}">{{ __('common.ui.home') }}</a>
                <span>/</span>
                <a href="{{ route('public.requests.index') }}">{{ __('requests.title') }}</a>
                <span>/</span>
                <span>{{ $request->title }}</span>
            </nav>

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
                                        <span class="khezana-attribute-name">{{ $attribute->name }}:</span>
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

                    <!-- Next Steps Guide -->
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

                    <!-- CTA Button -->
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

                    <!-- Offer Form (Hidden by default) -->
                    @auth
                        <div id="offerForm" class="khezana-contact-form" style="display: none;">
                            <h3 class="khezana-section-title-small">{{ __('common.ui.submit_offer') }}</h3>
                            <form method="POST" action="{{ route('public.requests.offer', $request->id) }}">
                                @csrf
                                <div class="khezana-form-group">
                                    <label class="khezana-form-label">{{ __('items.fields.operation_type') }}</label>
                                    <div class="khezana-filter-options">
                                        <label class="khezana-filter-option">
                                            <input type="radio" name="operation_type" value="sell" required>
                                            <span>{{ __('items.operation_types.sell') }}</span>
                                        </label>
                                        <label class="khezana-filter-option">
                                            <input type="radio" name="operation_type" value="rent" required>
                                            <span>{{ __('items.operation_types.rent') }}</span>
                                        </label>
                                        <label class="khezana-filter-option">
                                            <input type="radio" name="operation_type" value="donate" required>
                                            <span>{{ __('items.operation_types.donate') }}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="khezana-form-group" id="priceGroup" style="display: none;">
                                    <label class="khezana-form-label">{{ __('items.fields.price') }}</label>
                                    <input type="number" name="price" class="khezana-form-input" step="0.01"
                                        min="0">
                                </div>
                                <div class="khezana-form-group" id="depositGroup" style="display: none;">
                                    <label class="khezana-form-label">{{ __('items.fields.deposit_amount') }}</label>
                                    <input type="number" name="deposit_amount" class="khezana-form-input" step="0.01"
                                        min="0">
                                </div>
                                <div class="khezana-form-group">
                                    <label class="khezana-form-label">{{ __('common.ui.your_message') }}</label>
                                    <textarea name="message" class="khezana-form-input" rows="4"
                                        placeholder="{{ __('common.ui.write_your_message') }}"></textarea>
                                </div>
                                <div class="khezana-form-actions">
                                    <button type="submit" class="khezana-btn khezana-btn-primary">
                                        {{ __('common.ui.submit_offer') }}
                                    </button>
                                    <button type="button" onclick="hideOfferForm()" class="khezana-btn khezana-btn-secondary">
                                        {{ __('common.actions.cancel') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endauth

                    <!-- Additional Info -->
                    <div class="khezana-item-additional-info">
                        <div class="khezana-info-item">
                            <span class="khezana-info-icon">📅</span>
                            <span class="khezana-info-text">{{ __('common.ui.published') }}
                                {{ $request->createdAtFormatted }}</span>
                        </div>
                        @if ($request->user)
                            <div class="khezana-info-item">
                                <span class="khezana-info-icon">👤</span>
                                <span class="khezana-info-text">{{ __('common.ui.from') }}
                                    {{ $request->user->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple JavaScript for Offer Form -->
    <script>
        function showOfferForm() {
            document.getElementById('offerForm').style.display = 'block';
            document.getElementById('offerForm').scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        function hideOfferForm() {
            document.getElementById('offerForm').style.display = 'none';
        }

        // Show/hide price and deposit fields based on operation type
        document.addEventListener('DOMContentLoaded', function() {
            const operationTypes = document.querySelectorAll('input[name="operation_type"]');
            const priceGroup = document.getElementById('priceGroup');
            const depositGroup = document.getElementById('depositGroup');

            operationTypes.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'donate') {
                        priceGroup.style.display = 'none';
                        depositGroup.style.display = 'none';
                    } else if (this.value === 'rent') {
                        priceGroup.style.display = 'block';
                        depositGroup.style.display = 'block';
                    } else {
                        priceGroup.style.display = 'block';
                        depositGroup.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection
