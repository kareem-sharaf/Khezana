@props([
    'placeholder' => __('Search items and requests...'),
    'action' => null,
    'value' => null,
])

<form method="GET" 
      action="{{ $action ?? route('public.items.index') }}" 
      class="search-bar">
    <div class="search-bar__input-wrapper">
        <input type="search"
               name="search"
               value="{{ old('search', $value ?? request('search')) }}"
               placeholder="{{ $placeholder }}"
               class="search-bar__input"
               aria-label="{{ __('Search') }}">
        <button type="submit" class="search-bar__button" aria-label="{{ __('Search') }}">
            🔍
        </button>
    </div>
</form>
