{{-- Privacy Policy Page --}}
@extends('pages._layout')

@section('title', __('pages.privacy.title'))

@push('meta')
    <meta name="description" content="{{ __('pages.privacy.meta_description') }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ route('pages.privacy') }}">
@endpush

@section('page-content')
    <div class="khezana-static-page__sections">
        @foreach(__('pages.privacy.sections') as $key => $section)
            <section class="khezana-static-page__section" id="section-{{ $key }}">
                <h2 class="khezana-static-page__section-title">{{ $section['title'] }}</h2>
                <div class="khezana-static-page__section-content">
                    <p>{{ $section['content'] }}</p>
                </div>
            </section>
        @endforeach
    </div>

    <div class="khezana-static-page__footer">
        <p class="khezana-static-page__note">
            {{ __('pages.privacy.last_updated') }}: <time datetime="{{ $lastUpdated ?? now()->format('Y-m-d') }}">{{ $lastUpdated ?? now()->format('Y-m-d') }}</time>
        </p>
    </div>
@endsection
