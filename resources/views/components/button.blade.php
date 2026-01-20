@props([
    'type' => 'primary',
    'size' => 'medium',
    'disabled' => false,
    'href' => null,
])

@php
    $classes = [
        'btn',
        'btn-' . $type,
        'btn-' . $size,
    ];
    
    if ($disabled) {
        $classes[] = 'btn--disabled';
    }
    
    $classString = implode(' ', $classes);
@endphp

@if($href)
    <a href="{{ $href }}" 
       class="{{ $classString }}"
       @if($disabled) aria-disabled="true" @endif>
        {{ $slot }}
    </a>
@else
    <button type="{{ $attributes->get('type', 'button') }}"
            class="{{ $classString }}"
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except(['type', 'class']) }}>
        {{ $slot }}
    </button>
@endif
