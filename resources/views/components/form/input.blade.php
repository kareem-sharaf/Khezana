@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
])

<div class="form-group">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required)
            <span class="form-required" aria-label="{{ __('Required') }}">*</span>
        @endif
    </label>
    
    <input 
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        class="form-input @error($name) form-input--error @enderror"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except(['class', 'type', 'id', 'name', 'value', 'placeholder']) }}
    />
    
    @error($name)
        <div class="form-error" role="alert">
            {{ $message }}
        </div>
    @elseif($error)
        <div class="form-error" role="alert">
            {{ $error }}
        </div>
    @enderror
</div>
