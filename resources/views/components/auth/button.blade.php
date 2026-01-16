@props(['type' => 'submit', 'variant' => 'primary'])

<button 
    type="{{ $type }}"
    class="btn btn-{{ $variant }}"
    {{ $attributes }}
>
    {{ $slot }}
</button>
