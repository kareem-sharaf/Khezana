<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('meta')

    <title>@yield('title', config('app.name', 'Khezana'))</title>

    @yield('canonical')

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>

<body>
    @include('partials.header')

    <main id="main-content" role="main">
        @if(session('message'))
            <x-alert type="info" :dismissible="true">
                {{ session('message') }}
            </x-alert>
        @endif
        
        @if(session('success'))
            <x-alert type="success" :dismissible="true">
                {{ session('success') }}
            </x-alert>
        @endif
        
        @if(session('error'))
            <x-alert type="error" :dismissible="true">
                {{ session('error') }}
            </x-alert>
        @endif
        
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')
</body>

</html>
