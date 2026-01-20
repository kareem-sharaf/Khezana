@props([
    'name',
    'label',
    'value' => null,
    'placeholder' => null,
    'rows' => 4,
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
    
    <textarea 
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        class="form-textarea @error($name) form-textarea--error @enderror"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except(['class', 'id', 'name', 'rows', 'placeholder']) }}
    >{{ old($name, $value) }}</textarea>
    
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
