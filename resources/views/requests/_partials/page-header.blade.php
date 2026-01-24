{{-- Page Header Partial for Requests --}}
{{-- Usage: @include('requests._partials.page-header', ['requests' => $requests, 'showCreateButton' => true]) --}}

@php
    $title = $title ?? __('common.ui.my_requests_page');
    $subtitle = $subtitle ?? $requests->total() . ' ' . __('requests.plural');
    $showCreateButton = $showCreateButton ?? true;
    $createButtonText = $createButtonText ?? __('common.ui.add_request');
    $createButtonRoute = $createButtonRoute ?? route('requests.create');
@endphp

<div class="khezana-page-header">
    <div
        style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--khezana-spacing-md);">
        <div>
            <h1 class="khezana-page-title">{{ $title }}</h1>
            <p class="khezana-page-subtitle">{{ $subtitle }}</p>
        </div>
        @if ($showCreateButton)
            <a href="{{ $createButtonRoute }}" class="khezana-btn khezana-btn-primary">
                {{ $createButtonText }}
            </a>
        @endif
    </div>
</div>
