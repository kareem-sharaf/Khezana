{{-- How It Works Page --}}
@extends('pages._layout')

@section('title', __('pages.how_it_works.title'))

@push('meta')
    <meta name="description" content="{{ __('pages.how_it_works.meta_description') }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ route('pages.how-it-works') }}">
@endpush

@section('page-content')
    {{-- Introduction --}}
    <section class="khezana-static-page__intro">
        <h2 class="khezana-static-page__section-title">{{ __('pages.how_it_works.sections.intro.title') }}</h2>
        <p class="khezana-static-page__intro-text">{{ __('pages.how_it_works.sections.intro.content') }}</p>
    </section>

    {{-- For Sellers --}}
    <section class="khezana-static-page__section khezana-static-page__section--process">
        <h2 class="khezana-static-page__section-title">{{ __('pages.how_it_works.sections.for_sellers.title') }}</h2>
        <div class="khezana-process-steps">
            @foreach(__('pages.how_it_works.sections.for_sellers.steps') as $step)
                <div class="khezana-process-step">
                    <div class="khezana-process-step__number">{{ $loop->iteration }}</div>
                    <div class="khezana-process-step__content">
                        <h3 class="khezana-process-step__title">{{ $step['title'] }}</h3>
                        <p class="khezana-process-step__description">{{ $step['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- For Buyers --}}
    <section class="khezana-static-page__section khezana-static-page__section--process">
        <h2 class="khezana-static-page__section-title">{{ __('pages.how_it_works.sections.for_buyers.title') }}</h2>
        <div class="khezana-process-steps">
            @foreach(__('pages.how_it_works.sections.for_buyers.steps') as $step)
                <div class="khezana-process-step">
                    <div class="khezana-process-step__number">{{ $loop->iteration }}</div>
                    <div class="khezana-process-step__content">
                        <h3 class="khezana-process-step__title">{{ $step['title'] }}</h3>
                        <p class="khezana-process-step__description">{{ $step['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- For Renters --}}
    <section class="khezana-static-page__section khezana-static-page__section--process">
        <h2 class="khezana-static-page__section-title">{{ __('pages.how_it_works.sections.for_renters.title') }}</h2>
        <div class="khezana-process-steps">
            @foreach(__('pages.how_it_works.sections.for_renters.steps') as $step)
                <div class="khezana-process-step">
                    <div class="khezana-process-step__number">{{ $loop->iteration }}</div>
                    <div class="khezana-process-step__content">
                        <h3 class="khezana-process-step__title">{{ $step['title'] }}</h3>
                        <p class="khezana-process-step__description">{{ $step['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
