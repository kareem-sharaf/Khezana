@props([
    'type',
    'label' => null,
    'variant' => null,
])

@php
    $variant = $variant ?? match($type) {
        'available', 'approved', 'open', 'accepted' => 'success',
        'unavailable', 'rejected', 'closed', 'cancelled' => 'danger',
        'pending' => 'warning',
        'fulfilled', 'archived' => 'info',
        default => 'default',
    };
    
    // Use label prop if provided, otherwise use slot content
    $slotContent = trim((string) $slot);
    $displayLabel = $label ?? ($slotContent ?: $type);
    $ariaLabel = $displayLabel;
@endphp

<span class="badge badge--{{ $variant }}" 
      data-type="{{ $type }}"
      aria-label="{{ $ariaLabel }}">
    @if($label)
        {{ $label }}
    @else
        {{ $slot }}
    @endif
</span>
