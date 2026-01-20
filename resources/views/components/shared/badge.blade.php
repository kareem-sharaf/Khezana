@props([
    'type',
    'label',
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
@endphp

<span class="badge badge--{{ $variant }}" 
      data-type="{{ $type }}"
      aria-label="{{ $label }}">
    {{ $label }}
</span>
