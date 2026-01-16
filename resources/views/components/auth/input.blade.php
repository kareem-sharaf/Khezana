@props(['name', 'type' => 'text', 'label' => null, 'required' => false, 'hint' => null, 'error' => null])

<div class="form-group">
    @if($label)
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required)
        <span class="required">*</span>
        @endif
    </label>
    @endif
    
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name) }}"
        class="form-input @error($name) error @enderror"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >
    
    @if($hint && !$errors->has($name))
    <span class="form-hint">{{ $hint }}</span>
    @endif
    
    @error($name)
    <span class="form-error">{{ $message }}</span>
    @enderror
    
    @if($error && !$errors->has($name))
    <span class="form-error">{{ $error }}</span>
    @endif
</div>
