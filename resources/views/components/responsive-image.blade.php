{{-- Phase 3.3: WebP + fallback, optional srcset --}}
@props([
    'url',
    'urlWebp' => null,
    'alt' => '',
    'class' => '',
    'loading' => 'lazy',
    'decoding' => 'async',
    'sizes' => '(max-width: 768px) 100vw, 50vw',
])

@if($urlWebp)
<picture>
    <source type="image/webp" srcset="{{ $urlWebp }}">
    <img src="{{ $url }}" alt="{{ $alt }}" class="{{ $class }}" loading="{{ $loading }}" decoding="{{ $decoding }}"
        srcset="{{ $url }} 1920w" sizes="{{ $sizes }}">
</picture>
@else
<img src="{{ $url }}" alt="{{ $alt }}" class="{{ $class }}" loading="{{ $loading }}" decoding="{{ $decoding }}"
    srcset="{{ $url }} 1920w" sizes="{{ $sizes }}">
@endif
