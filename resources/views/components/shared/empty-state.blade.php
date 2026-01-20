@props([
    'title',
    'message' => null,
    'icon' => null,
    'action' => null,
])

<div class="empty-state" role="status" aria-live="polite">
    @if($icon)
        <div class="empty-state__icon" aria-hidden="true">
            {{ $icon }}
        </div>
    @endif
    
    <h2 class="empty-state__title">{{ $title }}</h2>
    
    @if($message)
        <p class="empty-state__message">{{ $message }}</p>
    @endif
    
    @if($action)
        <a href="{{ $action['url'] }}" class="empty-state__action">
            {{ $action['label'] }}
        </a>
    @endif
</div>
