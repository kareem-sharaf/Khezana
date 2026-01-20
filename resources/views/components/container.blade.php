@props([
    'size' => 'lg',
])

<div class="container container--{{ $size }}">
    {{ $slot }}
</div>
