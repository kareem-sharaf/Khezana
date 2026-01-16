@props(['type' => 'success', 'message' => null])

@if($message || $slot->isNotEmpty())
<div class="alert alert-{{ $type }}">
    {{ $message ?? $slot }}
</div>
@endif
