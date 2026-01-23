{{-- Profile Edit Page --}}
@extends('layouts.app')

@section('title', __('profile.edit_title') . ' - ' . config('app.name'))

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
                <span>{{ __('profile.edit_title') }}</span>
            </nav>

            {{-- Success Message --}}
            @if(session('status') === 'profile-updated')
                <div class="khezana-alert khezana-alert--success">
                    <p>{{ session('message', __('profile.update_success')) }}</p>
                </div>
            @endif

            {{-- Profile Content --}}
            <div class="khezana-profile-content">
                <div class="khezana-profile-sidebar">
                    @include('profile._partials.navigation')
                </div>

                <main class="khezana-profile-main">
                    <section class="khezana-profile-card">
                        <h2 class="khezana-profile-card__title">{{ __('profile.edit_personal_info') }}</h2>
                        
                        <form method="POST" action="{{ route('profile.update') }}" class="khezana-form">
                            @csrf
                            @method('patch')

                            {{-- Name --}}
                            <div class="khezana-form-group">
                                <label for="name" class="khezana-form-label">
                                    {{ __('profile.name') }}
                                    <span class="khezana-required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name', $viewModel->name) }}" 
                                    class="khezana-form-input @error('name') khezana-form-input--error @enderror"
                                    required
                                    autofocus
                                >
                                @error('name')
                                    <span class="khezana-form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="khezana-form-group">
                                <label for="email" class="khezana-form-label">
                                    {{ __('profile.email') }}
                                    <span class="khezana-required">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email', $viewModel->email) }}" 
                                    class="khezana-form-input @error('email') khezana-form-input--error @enderror"
                                    required
                                >
                                @error('email')
                                    <span class="khezana-form-error">{{ $message }}</span>
                                @enderror
                                @if($viewModel->isEmailVerified())
                                    <p class="khezana-form-help">
                                        {{ __('profile.email_verified_note') }}
                                    </p>
                                @else
                                    <p class="khezana-form-help khezana-form-help--warning">
                                        {{ __('profile.email_not_verified_note') }}
                                    </p>
                                @endif
                            </div>

                            {{-- Phone --}}
                            <div class="khezana-form-group">
                                <label for="phone" class="khezana-form-label">
                                    {{ __('profile.phone') }}
                                </label>
                                <input 
                                    type="tel" 
                                    id="phone" 
                                    name="phone" 
                                    value="{{ old('phone', $viewModel->phone) }}" 
                                    class="khezana-form-input @error('phone') khezana-form-input--error @enderror"
                                    placeholder="{{ __('profile.phone_placeholder') }}"
                                >
                                @error('phone')
                                    <span class="khezana-form-error">{{ $message }}</span>
                                @enderror
                            </div>


                            {{-- Actions --}}
                            <div class="khezana-form-actions">
                                <button type="submit" class="khezana-btn khezana-btn--primary">
                                    {{ __('profile.save_changes') }}
                                </button>
                                <a href="{{ route('profile.show') }}" class="khezana-btn khezana-btn--secondary">
                                    {{ __('common.actions.cancel') }}
                                </a>
                            </div>
                        </form>
                    </section>
                </main>
            </div>
        </div>
    </div>
@endsection
