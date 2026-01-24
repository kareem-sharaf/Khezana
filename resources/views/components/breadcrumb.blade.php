@props([
    'items' => [], // Array of ['label' => '...', 'url' => '...' or null]
    'homeLabel' => null,
    'homeUrl' => null,
])

@php
    $homeLabel = $homeLabel ?? __('common.ui.home') ?? 'الرئيسية';
    $homeUrl = $homeUrl ?? route('home');
    
    // Build breadcrumb items
    $breadcrumbs = [];
    
    // Always add home as first item
    $breadcrumbs[] = [
        'label' => $homeLabel,
        'url' => $homeUrl,
    ];
    
    // Add custom items
    foreach ($items as $item) {
        $breadcrumbs[] = $item;
    }
@endphp

<nav class="khezana-breadcrumb" aria-label="{{ __('common.ui.breadcrumb') ?? 'مسار التنقل' }}">
    <ol class="khezana-breadcrumb__list">
        @foreach ($breadcrumbs as $index => $breadcrumb)
            <li class="khezana-breadcrumb__item">
                @if ($breadcrumb['url'] ?? null)
                    <a 
                        href="{{ $breadcrumb['url'] }}" 
                        class="khezana-breadcrumb__link"
                        @if($loop->last) aria-current="page" @endif>
                        {{ $breadcrumb['label'] }}
                    </a>
                @else
                    <span class="khezana-breadcrumb__current" aria-current="page">
                        {{ $breadcrumb['label'] }}
                    </span>
                @endif
                
                @if (!$loop->last)
                    <span class="khezana-breadcrumb__separator" aria-hidden="true">/</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
