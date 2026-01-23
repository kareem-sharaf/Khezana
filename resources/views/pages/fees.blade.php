{{-- Fees and Commissions Page --}}
@extends('pages._layout')

@section('title', __('pages.fees.title'))

@push('meta')
    <meta name="description" content="{{ __('pages.fees.meta_description') }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ route('pages.fees') }}">
@endpush

@section('page-content')
    {{-- Introduction --}}
    <section class="khezana-static-page__section">
        <h2 class="khezana-static-page__section-title">{{ __('pages.fees.sections.introduction.title') }}</h2>
        <div class="khezana-static-page__section-content">
            <p>{{ __('pages.fees.sections.introduction.content') }}</p>
        </div>
    </section>

    {{-- Service Fee --}}
    <section class="khezana-static-page__section khezana-static-page__section--highlight">
        <h2 class="khezana-static-page__section-title">{{ __('pages.fees.sections.service_fee.title') }}</h2>
        <div class="khezana-static-page__section-content">
            <p>{{ __('pages.fees.sections.service_fee.content') }}</p>
            
            @php
                $feePercent = \App\Models\Setting::deliveryServiceFeePercent();
            @endphp
            
            <div class="khezana-fee-card">
                <div class="khezana-fee-card__label">{{ __('pages.fees.sections.service_fee.percentage') }}</div>
                <div class="khezana-fee-card__value">{{ number_format($feePercent, 1) }}%</div>
            </div>
            
            <p class="khezana-static-page__note">
                <strong>{{ __('pages.fees.sections.service_fee.note') }}</strong>
            </p>
        </div>
    </section>

    {{-- Free Listing --}}
    <section class="khezana-static-page__section">
        <h2 class="khezana-static-page__section-title">{{ __('pages.fees.sections.free_listing.title') }}</h2>
        <div class="khezana-static-page__section-content">
            <p>{{ __('pages.fees.sections.free_listing.content') }}</p>
        </div>
    </section>

    {{-- Payment Methods --}}
    <section class="khezana-static-page__section">
        <h2 class="khezana-static-page__section-title">{{ __('pages.fees.sections.payment_methods.title') }}</h2>
        <div class="khezana-static-page__section-content">
            <p>{{ __('pages.fees.sections.payment_methods.content') }}</p>
        </div>
    </section>

    {{-- Refunds --}}
    <section class="khezana-static-page__section">
        <h2 class="khezana-static-page__section-title">{{ __('pages.fees.sections.refunds.title') }}</h2>
        <div class="khezana-static-page__section-content">
            <p>{{ __('pages.fees.sections.refunds.content') }}</p>
        </div>
    </section>

    {{-- Contact --}}
    <section class="khezana-static-page__section">
        <h2 class="khezana-static-page__section-title">{{ __('pages.fees.sections.contact.title') }}</h2>
        <div class="khezana-static-page__section-content">
            <p>{{ __('pages.fees.sections.contact.content') }}</p>
        </div>
    </section>

    <div class="khezana-static-page__footer">
        <p class="khezana-static-page__note">
            {{ __('pages.fees.last_updated') }}: <time datetime="{{ $lastUpdated ?? now()->format('Y-m-d') }}">{{ $lastUpdated ?? now()->format('Y-m-d') }}</time>
        </p>
    </div>
@endsection
