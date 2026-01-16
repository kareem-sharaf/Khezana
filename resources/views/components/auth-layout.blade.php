<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? __('Login') }} - {{ config('app.name', 'Khezana') }}</title>
    
    <!-- Lightweight CSS - No external dependencies -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo Section -->
            <div class="auth-logo">
                <a href="{{ route('welcome', [], false) }}">
                    <x-application-logo style="width: 64px; height: 64px; margin: 0 auto; display: block;" />
                </a>
            </div>
            
            <!-- Title Section -->
            @if(isset($title) || isset($subtitle))
            <div>
                @if(isset($title))
                <h1 class="auth-title">{{ $title }}</h1>
                @endif
                @if(isset($subtitle))
                <p class="auth-subtitle">{{ $subtitle }}</p>
                @endif
            </div>
            @endif
            
            <!-- Content Slot -->
            {{ $slot }}
        </div>
    </div>
</body>
</html>
