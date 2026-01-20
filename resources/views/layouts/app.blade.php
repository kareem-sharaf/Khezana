<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('meta')

    <title>@yield('title', config('app.name', 'Khezana'))</title>

    @yield('canonical')

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('head')
</head>

<body>
    @include('partials.header')

    <main id="main-content" role="main">
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')
</body>

</html>
