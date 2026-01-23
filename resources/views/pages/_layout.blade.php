{{-- Static Page Layout - Reusable layout for static pages --}}
@extends('layouts.app')

@section('title', ($title ?? '') . ' - ' . config('app.name'))

@push('meta')
    @if(isset($metaDescription))
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="{{ $title ?? '' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if(isset($metaDescription))
        <meta property="og:description" content="{{ $metaDescription }}">
    @endif
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $title ?? '' }}">
    @if(isset($metaDescription))
        <meta name="twitter:description" content="{{ $metaDescription }}">
    @endif
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/static-pages.css') }}">
@endpush

@section('content')
    <div class="khezana-static-page">
        <div class="khezana-container">
            {{-- Breadcrumb --}}
            <nav class="khezana-breadcrumb" aria-label="{{ __('common.ui.breadcrumb') }}">
                <a href="{{ route('home') }}">{{ __('common.ui.home') }}</a>
                <span>/</span>
                <span>{{ $title ?? '' }}</span>
            </nav>

            {{-- Page Header --}}
            <header class="khezana-static-page__header">
                <h1 class="khezana-static-page__title">{{ $title ?? '' }}</h1>
                @if(isset($lastUpdated))
                    <p class="khezana-static-page__meta">
                        {{ __('pages.terms.last_updated') }}: <time datetime="{{ $lastUpdated }}">{{ $lastUpdated }}</time>
                    </p>
                @endif
            </header>

            {{-- Page Content --}}
            <main class="khezana-static-page__content" role="main">
                @yield('page-content')
            </main>
        </div>
    </div>
@endsection
