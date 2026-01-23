{{-- Password Update Page --}}
@extends('layouts.app')

@section('title', __('profile.password.title') . ' - ' . config('app.name'))

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
                <a href="{{ route('profile.show') }}">{{ __('profile.title') }}</a>
                <span>/</span>
                <span>{{ __('profile.password.title') }}</span>
            </nav>

            {{-- Success Message --}}
            @if(session('status') === 'password-updated')
                <div class="khezana-alert khezana-alert--success">
                    <p>{{ session('message', __('profile.password.update_success')) }}</p>
                </div>
            @endif

            {{-- Profile Content --}}
            <div class="khezana-profile-content">
                <div class="khezana-profile-sidebar">
                    @include('profile._partials.navigation')
                </div>

                <main class="khezana-profile-main">
                    <section class="khezana-profile-card">
                        <h2 class="khezana-profile-card__title">{{ __('profile.password.update_password') }}</h2>
                        <p class="khezana-profile-card__description">
                            {{ __('profile.password.description') }}
                        </p>
                        
                        <form method="POST" action="{{ route('profile.password.update') }}" class="khezana-form">
                            @csrf
                            @method('put')

                            {{-- Current Password --}}
                            <div class="khezana-form-group">
                                <label for="current_password" class="khezana-form-label">
                                    {{ __('profile.password.current') }}
                                    <span class="khezana-required">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    id="current_password" 
                                    name="current_password" 
                                    class="khezana-form-input @error('current_password') khezana-form-input--error @enderror"
                                    required
                                    autofocus
                                >
                                @error('current_password')
                                    <span class="khezana-form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- New Password --}}
                            <div class="khezana-form-group">
                                <label for="password" class="khezana-form-label">
                                    {{ __('profile.password.new') }}
                                    <span class="khezana-required">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="khezana-form-input @error('password') khezana-form-input--error @enderror"
                                    required
                                >
                                @error('password')
                                    <span class="khezana-form-error">{{ $message }}</span>
                                @enderror
                                <p class="khezana-form-help">
                                    {{ __('profile.password.requirements') }}
                                </p>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="khezana-form-group">
                                <label for="password_confirmation" class="khezana-form-label">
                                    {{ __('profile.password.confirm') }}
                                    <span class="khezana-required">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    class="khezana-form-input"
                                    required
                                >
                            </div>

                            {{-- Actions --}}
                            <div class="khezana-form-actions">
                                <button type="submit" class="khezana-btn khezana-btn--primary">
                                    {{ __('profile.password.update') }}
                                </button>
                                <a href="{{ route('profile.show') }}" class="khezana-btn khezana-btn--secondary">
                                    {{ __('common.actions.cancel') }}
                                </a>
                            </div>
                        </form>
                    </section>

                    {{-- Security Tips --}}
                    <section class="khezana-profile-card">
                        <h3 class="khezana-profile-card__title">{{ __('profile.password.security_tips') }}</h3>
                        <ul class="khezana-list khezana-list--bulleted">
                            <li>{{ __('profile.password.tip1') }}</li>
                            <li>{{ __('profile.password.tip2') }}</li>
                            <li>{{ __('profile.password.tip3') }}</li>
                            <li>{{ __('profile.password.tip4') }}</li>
                        </ul>
                    </section>
                </main>
            </div>
        </div>
    </div>
@endsection
