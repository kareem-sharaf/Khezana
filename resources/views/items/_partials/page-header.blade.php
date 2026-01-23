{{-- Page Header Partial for Items --}}
{{-- Usage: @include('items._partials.page-header', ['items' => $items, 'showCreateButton' => true]) --}}

@php
    $title = $title ?? __('common.ui.my_items_page');
    $subtitle = $subtitle ?? $items->total() . ' ' . __('items.plural');
    $showCreateButton = $showCreateButton ?? true;
    $createButtonText = $createButtonText ?? __('common.ui.add_new_item');
    $createButtonRoute = $createButtonRoute ?? route('items.create');
    $secondaryButton = $secondaryButton ?? null;
@endphp

<div class="khezana-page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--khezana-spacing-md);">
        <div>
            <h1 class="khezana-page-title">{{ $title }}</h1>
            <p class="khezana-page-subtitle">{{ $subtitle }}</p>
        </div>
        <div style="display: flex; gap: var(--khezana-spacing-sm); flex-wrap: wrap;">
            @if ($secondaryButton)
                <a href="{{ $secondaryButton['route'] }}" class="khezana-btn khezana-btn-secondary">
                    {{ $secondaryButton['text'] }}
                </a>
            @endif
            @if ($showCreateButton)
                <a href="{{ $createButtonRoute }}" class="khezana-btn khezana-btn-primary">
                    {{ $createButtonText }}
                </a>
            @endif
        </div>
    </div>
</div>
