{{-- Profile Show Page --}}
@extends('layouts.app')

@section('title', __('profile.title') . ' - ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/profile.css') }}">
@endpush

@section('content')
    <div class="khezana-profile-page">
        <div class="khezana-container">
            {{-- Breadcrumb --}}
            <nav class="khezana-breadcrumb" aria-label="{{ __('common.ui.breadcrumb') }}">
                <a href="{{ route('home') }}">{{ __('common.ui.home') }}</a>
                <span>/</span>
                <span>{{ __('profile.title') }}</span>
            </nav>

            {{-- Page Header --}}
            <header class="khezana-profile-header">
                <div class="khezana-profile-header__content">
                    <div class="khezana-profile-avatar">
                        <div class="khezana-profile-avatar__image">
                            @if($viewModel->avatar)
                                <img src="{{ asset('storage/' . $viewModel->avatar) }}" alt="{{ $viewModel->name }}">
                            @else
                                <div class="khezana-profile-avatar__placeholder">
                                    {{ strtoupper(substr($viewModel->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="khezana-profile-header__info">
                        <h1 class="khezana-profile-header__name">{{ $viewModel->name }}</h1>
                        <p class="khezana-profile-header__email">{{ $viewModel->email }}</p>
                        @if($viewModel->isEmailVerified())
                            <span class="khezana-badge khezana-badge--success">
                                {{ __('profile.email_verified') }}
                            </span>
                        @else
                            <span class="khezana-badge khezana-badge--warning">
                                {{ __('profile.email_not_verified') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="khezana-profile-header__actions">
                    <a href="{{ route('profile.edit') }}" class="khezana-btn khezana-btn--primary">
                        {{ __('profile.edit_profile') }}
                    </a>
                </div>
            </header>

            {{-- Profile Content --}}
            <div class="khezana-profile-content">
                <div class="khezana-profile-sidebar">
                    @include('profile._partials.navigation')
                </div>

                <main class="khezana-profile-main">
                    {{-- Profile Info Card --}}
                    <section class="khezana-profile-card">
                        <h2 class="khezana-profile-card__title">{{ __('profile.personal_info') }}</h2>
                        <div class="khezana-profile-card__content">
                            <div class="khezana-profile-info">
                                <div class="khezana-profile-info__item">
                                    <span class="khezana-profile-info__label">{{ __('profile.name') }}</span>
                                    <span class="khezana-profile-info__value">{{ $viewModel->name }}</span>
                                </div>
                                <div class="khezana-profile-info__item">
                                    <span class="khezana-profile-info__label">{{ __('profile.email') }}</span>
                                    <span class="khezana-profile-info__value">{{ $viewModel->email }}</span>
                                </div>
                                @if($viewModel->phone)
                                    <div class="khezana-profile-info__item">
                                        <span class="khezana-profile-info__label">{{ __('profile.phone') }}</span>
                                        <span class="khezana-profile-info__value">{{ $viewModel->phone }}</span>
                                    </div>
                                @endif
                                <div class="khezana-profile-info__item">
                                    <span class="khezana-profile-info__label">{{ __('profile.member_since') }}</span>
                                    <span class="khezana-profile-info__value">{{ $viewModel->getMemberSinceFormatted() }}</span>
                                </div>
                            </div>
                        </div>
                    </section>

                </main>
            </div>
        </div>
    </div>
@endsection
