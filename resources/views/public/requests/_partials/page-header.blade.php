{{-- Page Header Partial for Public Requests --}}
{{-- Usage: @include('public.requests._partials.page-header', ['requests' => $requests]) --}}

@php
    $title = $title ?? __('requests.title');
    $subtitle = isset($requests) && $requests->total() > 0 ? $requests->total() . ' ' . __('requests.plural') : __('requests.title');
@endphp

<header class="khezana-page-header">
    <div class="khezana-page-header__inner">
        <div class="khezana-page-header__text">
            <h1 class="khezana-page-title">{{ $title }}</h1>
            <p class="khezana-page-subtitle" aria-live="polite">{{ $subtitle }}</p>
        </div>
        <div class="khezana-page-header__actions">
            @auth
                <a href="{{ route('requests.index') }}" class="khezana-btn khezana-btn-secondary khezana-page-header__cta">
                    {{ __('common.ui.my_requests') }}
                </a>
                <a href="{{ route('requests.create') }}" class="khezana-btn khezana-btn-primary khezana-page-header__cta">
                    {{ __('common.ui.add_request') }}
                </a>
            @else
                <a href="{{ route('public.requests.create-info') }}" class="khezana-btn khezana-btn-primary khezana-page-header__cta">
                    {{ __('common.ui.add_request') }}
                </a>
            @endauth
        </div>
    </div>
</header>
