<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
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
    
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <!-- Simple Logo Link -->
    <div class="khezana-auth-header">
        <a href="{{ route('home') }}" class="khezana-auth-logo">
            <span class="khezana-logo-text">خزانة</span>
        </a>
    </div>

    <!-- Main Content -->
    <main class="khezana-auth-main">
        @yield('content')
    </main>
    
    @stack('scripts')
</body>
</html>
