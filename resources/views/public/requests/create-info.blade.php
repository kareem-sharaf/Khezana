@extends('layouts.app')

@section('title', __('requests.info_page.title') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-request-info-page">
        <!-- Hero Section -->
        <section class="khezana-request-hero">
            <div class="khezana-container">
                <div class="khezana-request-hero-content">
                    <div class="khezana-request-icon">📝</div>
                    <h1 class="khezana-request-hero-title">{{ __('requests.info_page.hero_title') }}</h1>
                    <p class="khezana-request-hero-subtitle">
                        {{ __('requests.info_page.hero_subtitle') }}
                    </p>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="khezana-request-how">
            <div class="khezana-container">
                <h2 class="khezana-section-title">{{ __('requests.info_page.how_it_works_title') }}</h2>

                <div class="khezana-steps">
                    <div class="khezana-step">
                        <div class="khezana-step-number">1</div>
                        <h3 class="khezana-step-title">{{ __('requests.info_page.step1_title') }}</h3>
                        <p class="khezana-step-description">
                            {{ __('requests.info_page.step1_description') }}
                        </p>
                    </div>

                    <div class="khezana-step">
                        <div class="khezana-step-number">2</div>
                        <h3 class="khezana-step-title">{{ __('requests.info_page.step2_title') }}</h3>
                        <p class="khezana-step-description">
                            {{ __('requests.info_page.step2_description') }}
                        </p>
                    </div>

                    <div class="khezana-step">
                        <div class="khezana-step-number">3</div>
                        <h3 class="khezana-step-title">{{ __('requests.info_page.step3_title') }}</h3>
                        <p class="khezana-step-description">
                            {{ __('requests.info_page.step3_description') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits -->
        <section class="khezana-request-benefits">
            <div class="khezana-container">
                <h2 class="khezana-section-title">{{ __('requests.info_page.benefits_title') }}</h2>
                <div class="khezana-benefits-grid">
                    <div class="khezana-benefit-card">
                        <div class="khezana-benefit-icon">🎯</div>
                        <h3 class="khezana-benefit-title">{{ __('requests.info_page.benefit1_title') }}</h3>
                        <p class="khezana-benefit-text">
                            {{ __('requests.info_page.benefit1_text') }}
                        </p>
                    </div>

                    <div class="khezana-benefit-card">
                        <div class="khezana-benefit-icon">💰</div>
                        <h3 class="khezana-benefit-title">{{ __('requests.info_page.benefit2_title') }}</h3>
                        <p class="khezana-benefit-text">
                            {{ __('requests.info_page.benefit2_text') }}
                        </p>
                    </div>

                    <div class="khezana-benefit-card">
                        <div class="khezana-benefit-icon">🤝</div>
                        <h3 class="khezana-benefit-title">{{ __('requests.info_page.benefit3_title') }}</h3>
                        <p class="khezana-benefit-text">
                            {{ __('requests.info_page.benefit3_text') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Examples -->
        <section class="khezana-request-examples">
            <div class="khezana-container">
                <h2 class="khezana-section-title">{{ __('requests.info_page.examples_title') }}</h2>

                <div class="khezana-examples-grid">
                    <div class="khezana-example-card">
                        <div class="khezana-example-icon">👔</div>
                        <p class="khezana-example-text">
                            "{{ __('requests.info_page.example1_text') }}"
                        </p>
                    </div>

                    <div class="khezana-example-card">
                        <div class="khezana-example-icon">👗</div>
                        <p class="khezana-example-text">
                            "{{ __('requests.info_page.example2_text') }}"
                        </p>
                    </div>

                    <div class="khezana-example-card">
                        <div class="khezana-example-icon">👕</div>
                        <p class="khezana-example-text">
                            "{{ __('requests.info_page.example3_text') }}"
                        </p>
                    </div>

                    <div class="khezana-example-card">
                        <div class="khezana-example-icon">👶</div>
                        <p class="khezana-example-text">
                            "{{ __('requests.info_page.example4_text') }}"
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Response Types -->
        <section class="khezana-request-response-types">
            <div class="khezana-container">
                <h2 class="khezana-section-title">{{ __('requests.info_page.response_types_title') }}</h2>

                <div class="khezana-response-types-grid">
                    <div class="khezana-response-type-card">
                        <div class="khezana-response-type-icon khezana-response-type-sell">🛒</div>
                        <h3 class="khezana-response-type-title">{{ __('requests.info_page.response_sell_title') }}</h3>
                        <p class="khezana-response-type-text">
                            {{ __('requests.info_page.response_sell_text') }}
                        </p>
                    </div>

                    <div class="khezana-response-type-card">
                        <div class="khezana-response-type-icon khezana-response-type-rent">👔</div>
                        <h3 class="khezana-response-type-title">{{ __('requests.info_page.response_rent_title') }}</h3>
                        <p class="khezana-response-type-text">
                            {{ __('requests.info_page.response_rent_text') }}
                        </p>
                    </div>

                    <div class="khezana-response-type-card">
                        <div class="khezana-response-type-icon khezana-response-type-donate">❤️</div>
                        <h3 class="khezana-response-type-title">{{ __('requests.info_page.response_donate_title') }}</h3>
                        <p class="khezana-response-type-text">
                            {{ __('requests.info_page.response_donate_text') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="khezana-request-cta">
            <div class="khezana-container">
                <div class="khezana-request-cta-content">
                    <h2 class="khezana-request-cta-title">{{ __('requests.info_page.cta_title') }}</h2>
                    <p class="khezana-request-cta-text">
                        {{ __('requests.info_page.cta_text') }}
                    </p>
                    @auth
                        <a href="{{ route('requests.create', [], false) }}"
                            class="khezana-btn khezana-btn-primary khezana-btn-large">
                            {{ __('requests.info_page.cta_button_auth') }}
                        </a>
                    @else
                        <a href="{{ route('register', ['redirect' => route('requests.create', [], false)], false) }}"
                            class="khezana-btn khezana-btn-primary khezana-btn-large">
                            {{ __('requests.info_page.cta_button_guest') }}
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Browse Existing Requests -->
        <section class="khezana-request-browse">
            <div class="khezana-container">
                <div class="khezana-request-browse-content">
                    <h2 class="khezana-section-title">{{ __('requests.info_page.browse_title') }}</h2>
                    <a href="{{ route('public.requests.index') }}"
                        class="khezana-btn khezana-btn-secondary khezana-btn-large">
                        {{ __('requests.info_page.browse_button') }}
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
