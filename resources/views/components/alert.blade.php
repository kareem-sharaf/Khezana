@props([
    'type' => 'info',
    'dismissible' => false,
])

<div class="alert alert--{{ $type }}" role="alert">
    @if($dismissible)
        <button type="button" 
                class="alert__dismiss" 
                aria-label="{{ __('Close') }}"
                onclick="this.parentElement.remove()">
            ×
        </button>
    @endif
    
    <div class="alert__content">
        {{ $slot }}
    </div>
</div>
