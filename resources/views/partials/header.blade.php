<header class="site-header" role="banner">
    <div class="container">
        <nav class="site-nav" role="navigation" aria-label="{{ __('Main Navigation') }}">
            <a href="/" class="site-logo">{{ config('app.name', 'Khezana') }}</a>
            
            <ul class="site-nav__menu">
                <li><a href="{{ route('public.items.index') }}">{{ trans('items.plural') }}</a></li>
                <li><a href="{{ route('public.requests.index') }}">{{ trans('requests.plural') }}</a></li>
            </ul>
        </nav>
    </div>
</header>
