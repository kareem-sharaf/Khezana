{{-- Page Header Partial for Public Requests --}}
{{-- Usage: @include('public.requests._partials.page-header', ['requests' => $requests]) --}}

@php
    $title = $title ?? __('requests.title');
    $subtitle = $subtitle ?? $requests->total() . ' ' . __('requests.plural');
@endphp

<div class="khezana-page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--khezana-spacing-md);">
        <div>
            <h1 class="khezana-page-title">{{ $title }}</h1>
            <p class="khezana-page-subtitle">{{ $subtitle }}</p>
        </div>
        @auth
            <a href="{{ route('requests.index') }}" class="khezana-btn khezana-btn-secondary">
                {{ __('common.ui.my_requests') }}
            </a>
        @endauth
    </div>
</div>
