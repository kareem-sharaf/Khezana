{{-- Category Select Component with Tree Structure --}}
{{-- Usage: @include('components.category-select', ['categories' => $categories, 'name' => 'category_id', 'id' => 'category_id', 'selected' => old('category_id'), 'required' => true, 'attributes' => true]) --}}

@php
    // Ensure categories is a Collection
    $categories = isset($categories) && $categories ? (is_array($categories) ? collect($categories) : $categories) : collect();
    $name = $name ?? 'category_id';
    $id = $id ?? 'category_id';
    $selected = $selected ?? null;
    $required = $required ?? false;
    $placeholder = $placeholder ?? __('common.ui.select_category');
    $showAllOption = $showAllOption ?? false;
    $allOptionLabel = $allOptionLabel ?? __('common.ui.all_categories');
    $attributes = $attributes ?? false;
    $onchange = $onchange ?? null;
    $class = $class ?? 'khezana-form-input khezana-form-select';
@endphp

<select 
    name="{{ $name }}" 
    id="{{ $id }}" 
    class="{{ $class }}"
    @if($required) required @endif
    @if($onchange) onchange="{{ $onchange }}" @endif
>
    @if($showAllOption)
        <option value="">{{ $allOptionLabel }}</option>
    @elseif(!$required)
        <option value="">{{ $placeholder }}</option>
    @endif

    @foreach($categories as $category)
        @include('components.category-select-item', [
            'category' => $category,
            'level' => 0,
            'selected' => $selected,
            'attributes' => $attributes
        ])
    @endforeach
</select>
