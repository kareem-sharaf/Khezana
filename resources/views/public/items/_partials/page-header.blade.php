{{-- Page Header Partial for Public Items --}}
{{-- Usage: @include('public.items._partials.page-header', ['items' => $items]) --}}

@php
    $operationType = request('operation_type');
    $title = match($operationType) {
        'sell' => __('items.operation_types.sell') . ' - ' . __('items.title'),
        'rent' => __('items.operation_types.rent') . ' - ' . __('items.title'),
        'donate' => __('items.operation_types.donate') . ' - ' . __('items.title'),
        default => __('items.title'),
    };
    $subtitle = $items->total() . ' ' . __('items.plural');
@endphp

<header class="khezana-page-header">
    <div class="khezana-page-header__inner">
        <div class="khezana-page-header__text">
            <h1 class="khezana-page-title">{{ $title }}</h1>
            <p class="khezana-page-subtitle" aria-live="polite">{{ $subtitle }}</p>
        </div>
        @auth
            <a href="{{ route('items.index') }}" class="khezana-btn khezana-btn-secondary khezana-page-header__cta">
                {{ __('common.ui.my_items') }}
            </a>
        @endauth
    </div>
</header>
