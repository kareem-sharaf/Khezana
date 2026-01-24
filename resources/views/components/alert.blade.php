@props([
    'type' => 'info', // success, error, warning, info
    'title' => null,
    'message' => null,
    'dismissible' => false,
    'size' => 'md', // sm, md, lg
])

@php
    $icons = [
        'success' => '✓',
        'error' => '✕',
        'warning' => '⚠',
        'info' => 'ℹ',
    ];
    
    $icon = $icons[$type] ?? $icons['info'];
    $role = $type === 'error' ? 'alert' : 'status';
@endphp

<div 
    class="khezana-alert khezana-alert--{{ $type }} khezana-alert--{{ $size }}"
    role="{{ $role }}"
    @if($dismissible) x-data="{ show: true }" x-show="show" x-transition @endif>
    <span class="khezana-alert__icon" aria-hidden="true">{{ $icon }}</span>
    
    <div class="khezana-alert__content">
        @if($title)
            <h3 class="khezana-alert__title">{{ $title }}</h3>
        @endif
        
        @if($message || $slot->isNotEmpty())
            <p class="khezana-alert__message">
                {{ $message ?? $slot }}
            </p>
        @endif
    </div>
    
    @if($dismissible)
        <button 
            type="button"
            class="khezana-alert__close"
            @click="show = false"
            aria-label="{{ __('common.ui.close') ?? 'إغلاق' }}">
            <span aria-hidden="true">×</span>
        </button>
    @endif
</div>
