@props([
    'title',
    'message' => null,
    'description' => null,
    'icon' => null,
    'actionLabel' => null,
    'actionUrl' => null,
])

<div class="empty-state" role="status" aria-live="polite">
    @if($icon)
        <div class="empty-state__icon" aria-hidden="true">
            {{ $icon }}
        </div>
    @endif
    
    <h2 class="empty-state__title">{{ $title }}</h2>
    
    @if($description)
        <p class="empty-state__description">{{ $description }}</p>
    @elseif($message)
        <p class="empty-state__description">{{ $message }}</p>
    @endif
    
    @if($actionLabel && $actionUrl)
        <div class="empty-state__action">
            <x-button type="primary" href="{{ $actionUrl }}">
                {{ $actionLabel }}
            </x-button>
        </div>
    @endif
</div>
