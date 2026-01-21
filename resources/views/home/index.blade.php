@extends('layouts.home')

@section('content')
    <!-- Hero Section -->
    <section class="khezana-hero">
        <div class="khezana-container">
            <div class="khezana-hero-content">
                <h1 class="khezana-hero-title">
                    {{ __('common.home.hero_title') }}
                </h1>
                <p class="khezana-hero-subtitle">
                    {{ __('common.home.hero_subtitle') }}
                </p>
                <div class="khezana-hero-actions">
                    <a href="{{ route('public.items.index') }}" class="khezana-btn khezana-btn-primary khezana-btn-large khezana-btn-hero-primary">
                        {{ __('common.home.browse_offers') }}
                    </a>
                    <a href="{{ route('public.requests.create-info') }}"
                        class="khezana-btn khezana-btn-secondary khezana-btn-large khezana-btn-hero-secondary">
                        {{ __('common.home.request_clothing') }}
                    </a>
                </div>
                <!-- Microcopy Guidance -->
                <div class="khezana-hero-microcopy">
                    <p class="khezana-microcopy-text">
                        {{ __('common.home.hero_guide_simple') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="khezana-services">
        <div class="khezana-container">
            <div class="khezana-services-grid">
                <!-- Buy -->
                <a href="{{ route('public.items.index', ['operation_type' => 'sell']) }}" class="khezana-service-card">
                    <div class="khezana-service-icon">🛒</div>
                    <h3 class="khezana-service-title">{{ __('items.operation_types.sell') }}</h3>
                    <p class="khezana-service-hint">{{ __('common.home.service_sell_hint') }}</p>
                </a>

                <!-- Rent -->
                <a href="{{ route('public.items.index', ['operation_type' => 'rent']) }}" class="khezana-service-card">
                    <div class="khezana-service-icon">👔</div>
                    <h3 class="khezana-service-title">{{ __('items.operation_types.rent') }}</h3>
                    <p class="khezana-service-hint">{{ __('common.home.service_rent_hint') }}</p>
                </a>

                <!-- Donate -->
                <a href="{{ route('public.items.index', ['operation_type' => 'donate']) }}" class="khezana-service-card">
                    <div class="khezana-service-icon">❤️</div>
                    <h3 class="khezana-service-title">{{ __('items.operation_types.donate') }}</h3>
                    <p class="khezana-service-hint">{{ __('common.home.service_donate_hint') }}</p>
                </a>

                <!-- Request -->
                <a href="{{ route('public.requests.create-info') }}" class="khezana-service-card">
                    <div class="khezana-service-icon">📝</div>
                    <h3 class="khezana-service-title">{{ __('common.home.request_clothing') }}</h3>
                    <p class="khezana-service-hint">{{ __('common.home.service_request_hint') }}</p>
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="khezana-how-it-works">
        <div class="khezana-container">
            <h2 class="khezana-section-title">{{ __('common.home.how_it_works_title') }}</h2>
            <p class="khezana-section-subtitle">
                {{ __('common.home.how_it_works_subtitle') }}
            </p>

            <div class="khezana-steps">
                <div class="khezana-step">
                    <div class="khezana-step-number">1</div>
                    <h3 class="khezana-step-title">{{ __('common.home.step1_title') }}</h3>
                    <p class="khezana-step-description">
                        {{ __('common.home.step1_description') }}
                    </p>
                </div>

                <div class="khezana-step">
                    <div class="khezana-step-number">2</div>
                    <h3 class="khezana-step-title">{{ __('common.home.step2_title') }}</h3>
                    <p class="khezana-step-description">
                        {{ __('common.home.step2_description') }}
                    </p>
                </div>

                <div class="khezana-step">
                    <div class="khezana-step-number">3</div>
                    <h3 class="khezana-step-title">{{ __('common.home.step3_title') }}</h3>
                    <p class="khezana-step-description">
                        {{ __('common.home.step3_description') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Indicators -->
    <section class="khezana-trust">
        <div class="khezana-container">
            <div class="khezana-trust-grid">
                <div class="khezana-trust-item">
                    <div class="khezana-trust-icon">✓</div>
                    <h4 class="khezana-trust-title">{{ __('common.trust.reviewed') }}</h4>
                    <p class="khezana-trust-text">{{ __('common.trust.reviewed_text') }}</p>
                </div>
                <div class="khezana-trust-item">
                    <div class="khezana-trust-icon">🔒</div>
                    <h4 class="khezana-trust-title">{{ __('common.trust.secure') }}</h4>
                    <p class="khezana-trust-text">{{ __('common.trust.secure_text') }}</p>
                </div>
                <div class="khezana-trust-item">
                    <div class="khezana-trust-icon">👥</div>
                    <h4 class="khezana-trust-title">{{ __('common.trust.active') }}</h4>
                    <p class="khezana-trust-text">{{ __('common.trust.active_text') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Items - Sell -->
    <section class="khezana-featured">
        <div class="khezana-container">
            <h2 class="khezana-section-title">{{ __('common.home.featured_sell_title') }}</h2>
            <p class="khezana-section-subtitle">
                {{ __('common.home.featured_sell_subtitle') }}
            </p>

            @if (isset($featuredSell) && $featuredSell->count() > 0)
                <div class="khezana-items-grid">
                    @foreach ($featuredSell->take(6) as $item)
                        <a href="{{ route('public.items.show', ['id' => $item->id, 'slug' => $item->slug]) }}"
                            class="khezana-item-card">
                            @if ($item->primaryImage)
                                <img src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="{{ $item->title }}"
                                    class="khezana-item-image" loading="lazy">
                            @else
                                <div class="khezana-item-image"
                                    style="display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                    {{ __('common.ui.no_image') }}
                                </div>
                            @endif
                            <div class="khezana-item-content">
                                <h3 class="khezana-item-title">{{ $item->title }}</h3>
                                @if ($item->price)
                                    <div class="khezana-item-price">{{ number_format($item->price, 0) }}
                                        {{ __('common.ui.currency') }}</div>
                                @endif
                                <span class="khezana-item-badge">{{ __('items.operation_types.sell') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="khezana-view-all">
                    <a href="{{ route('public.items.index', ['operation_type' => 'sell']) }}"
                        class="khezana-btn khezana-btn-primary khezana-btn-large">
                        {{ __('common.home.view_all_sell') }}
                    </a>
                </div>
            @else
                <div class="khezana-empty-state">
                    <p class="khezana-empty-title">{{ __('common.home.no_items_available') }}</p>
                    <p class="khezana-empty-text">{{ __('common.home.no_items_available_hint') }}</p>
                    <a href="{{ route('public.requests.create-info') }}" class="khezana-btn khezana-btn-primary">
                        {{ __('common.home.request_clothing') }}
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Featured Items - Rent -->
    <section class="khezana-featured" style="background: var(--khezana-bg);">
        <div class="khezana-container">
            <h2 class="khezana-section-title">{{ __('common.home.featured_rent_title') }}</h2>
            <p class="khezana-section-subtitle">
                {{ __('common.home.featured_rent_subtitle') }}
            </p>

            @if (isset($featuredRent) && $featuredRent->count() > 0)
                <div class="khezana-items-grid">
                    @foreach ($featuredRent->take(6) as $item)
                        <a href="{{ route('public.items.show', ['id' => $item->id, 'slug' => $item->slug]) }}"
                            class="khezana-item-card">
                            @if ($item->primaryImage)
                                <img src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="{{ $item->title }}"
                                    class="khezana-item-image" loading="lazy">
                            @else
                                <div class="khezana-item-image"
                                    style="display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                    {{ __('common.ui.no_image') }}
                                </div>
                            @endif
                            <div class="khezana-item-content">
                                <h3 class="khezana-item-title">{{ $item->title }}</h3>
                                @if ($item->price)
                                    <div class="khezana-item-price">{{ number_format($item->price, 0) }}
                                        {{ __('common.ui.currency') }}{{ __('common.ui.per_day') }}</div>
                                @endif
                                <span class="khezana-item-badge">{{ __('items.operation_types.rent') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="khezana-view-all">
                    <a href="{{ route('public.items.index', ['operation_type' => 'rent']) }}"
                        class="khezana-btn khezana-btn-primary khezana-btn-large">
                        {{ __('common.home.view_all_rent') }}
                    </a>
                </div>
            @else
                <div class="khezana-empty-state">
                    <p class="khezana-empty-title">{{ __('common.home.no_items_available') }}</p>
                    <p class="khezana-empty-text">{{ __('common.home.no_items_available_hint') }}</p>
                    <a href="{{ route('public.requests.create-info') }}" class="khezana-btn khezana-btn-primary">
                        {{ __('common.home.request_clothing') }}
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Featured Items - Donate -->
    <section class="khezana-featured">
        <div class="khezana-container">
            <h2 class="khezana-section-title">{{ __('common.home.featured_donate_title') }}</h2>
            <p class="khezana-section-subtitle">
                {{ __('common.home.featured_donate_subtitle') }}
            </p>

            @if (isset($featuredDonate) && $featuredDonate->count() > 0)
                <div class="khezana-items-grid">
                    @foreach ($featuredDonate->take(6) as $item)
                        <a href="{{ route('public.items.show', ['id' => $item->id, 'slug' => $item->slug]) }}"
                            class="khezana-item-card">
                            @if ($item->primaryImage)
                                <img src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="{{ $item->title }}"
                                    class="khezana-item-image" loading="lazy">
                            @else
                                <div class="khezana-item-image"
                                    style="display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                    {{ __('common.ui.no_image') }}
                                </div>
                            @endif
                            <div class="khezana-item-content">
                                <h3 class="khezana-item-title">{{ $item->title }}</h3>
                                <span class="khezana-item-badge"
                                    style="background: var(--khezana-success); color: white;">
                                    {{ __('items.operation_types.donate') }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="khezana-view-all">
                    <a href="{{ route('public.items.index', ['operation_type' => 'donate']) }}"
                        class="khezana-btn khezana-btn-primary khezana-btn-large">
                        {{ __('common.home.view_all_donate') }}
                    </a>
                </div>
            @else
                <div class="khezana-empty-state">
                    <p class="khezana-empty-title">{{ __('common.home.no_items_available') }}</p>
                    <p class="khezana-empty-text">{{ __('common.home.no_items_available_hint') }}</p>
                    <a href="{{ route('public.requests.create-info') }}" class="khezana-btn khezana-btn-primary">
                        {{ __('common.home.request_clothing') }}
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Call to Action -->
    <section class="khezana-cta-final">
        <div class="khezana-container">
            <div class="khezana-cta-content">
                <p class="khezana-cta-text">{{ __('common.home.cta_title') }}</p>
                <div class="khezana-cta-actions">
                    <a href="{{ route('public.requests.create-info') }}"
                        class="khezana-btn khezana-btn-primary khezana-btn-large">
                        {{ __('common.home.cta_request') }}
                    </a>
                    <a href="{{ route('public.items.index') }}"
                        class="khezana-btn khezana-btn-secondary khezana-btn-large">
                        {{ __('common.home.cta_browse_all') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
