<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'تسجيل الدخول - ' . config('app.name', 'Khezana'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-improvements.css') }}">

    @stack('styles')
</head>

<body class="font-sans antialiased">
    <!-- Simple Logo Link -->
    <div class="khezana-auth-header">
        <a href="{{ route('home') }}" class="khezana-auth-logo">
            @if (file_exists(public_path('logo.svg')))
                <img src="{{ asset('logo.svg') }}" alt="{{ config('app.name') }}" class="khezana-logo-img">
                <span class="khezana-logo-text">خزانة</span>
            @elseif(file_exists(public_path('logo.png')))
                <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" class="khezana-logo-img">
                <span class="khezana-logo-text">خزانة</span>
            @else
                <span class="khezana-logo-text">خزانة</span>
            @endif
        </a>
    </div>

    <!-- Main Content -->
    <main class="khezana-auth-main">
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>
