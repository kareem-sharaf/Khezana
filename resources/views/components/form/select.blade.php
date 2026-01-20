@props([
    'name',
    'label',
    'options' => [],
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
    
    <select 
        id="{{ $name }}"
        name="{{ $name }}"
        class="form-select @error($name) form-select--error @enderror"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except(['class', 'id', 'name']) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
    
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
