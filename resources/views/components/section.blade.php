@props([
    'spacing' => 'md',
    'background' => 'transparent',
])

<section class="section section--spacing-{{ $spacing }} section--bg-{{ $background }}">
    {{ $slot }}
</section>
