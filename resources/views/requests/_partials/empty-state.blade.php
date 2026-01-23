{{-- Empty State Partial for Requests --}}
{{-- Usage: @include('requests._partials.empty-state', ['type' => 'user']) --}}

@php
    $type = $type ?? 'user'; // 'user' or 'public'
    $icon = $icon ?? '📝';
    $title = $title ?? __('common.ui.no_requests');
    $message = $message ?? __('common.ui.no_requests_message');
    $ctaText = $ctaText ?? __('common.ui.no_requests_cta');
    $ctaRoute = $ctaRoute ?? route('requests.create');
    $ctaClass = $ctaClass ?? 'khezana-btn-primary khezana-btn-large';
@endphp

<div class="khezana-empty-state" role="status" aria-live="polite">
    <div class="khezana-empty-icon" aria-hidden="true">{{ $icon }}</div>
    <h3 class="khezana-empty-title">{{ $title }}</h3>
    <p class="khezana-empty-text">{{ $message }}</p>
    <div class="khezana-empty-actions">
        <a href="{{ $ctaRoute }}" class="khezana-btn {{ $ctaClass }}">
            {{ $ctaText }}
        </a>
    </div>
</div>
