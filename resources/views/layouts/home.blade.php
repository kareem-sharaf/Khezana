<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Khezana') }} - {{ __('common.ui.about_us') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-improvements.css') }}">

    <!-- Simple Lazy Loading Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(function(img) {
                img.addEventListener('load', function() {
                    this.classList.add('loaded');
                });
                if (img.complete) {
                    img.classList.add('loaded');
                }
            });
        });
    </script>
</head>

<body class="font-sans antialiased">
    @include('partials.header')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    @include('partials.footer')
</body>

</html>
