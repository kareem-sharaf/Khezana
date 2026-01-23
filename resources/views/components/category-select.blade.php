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
        @if($category->children->count() > 0)
            {{-- Category with children: Use optgroup --}}
            <optgroup label="{{ $category->name }}">
                @if($attributes)
                    @php
                        $allAttrs = $category->getAllAttributes();
                        $allAttrs->each(function ($attr) {
                            if (
                                $attr instanceof \App\Models\Attribute &&
                                !$attr->relationLoaded('values')
                            ) {
                                $attr->load('values');
                            }
                        });
                        $categoryAttributes = $allAttrs
                            ->filter(function ($attr) {
                                return $attr instanceof \App\Models\Attribute;
                            })
                            ->map(function ($attr) {
                                return [
                                    'id' => $attr->id,
                                    'name' => $attr->name,
                                    'slug' => $attr->slug,
                                    'type' => $attr->type->value,
                                    'is_required' => $attr->is_required,
                                    'values' => $attr->values
                                        ? $attr->values->pluck('value')->toArray()
                                        : [],
                                ];
                            })
                            ->toJson();
                    @endphp
                    <option 
                        value="{{ $category->id }}"
                        {{ ($selected == $category->id) ? 'selected' : '' }}
                        @if($attributes) data-attributes="{{ $categoryAttributes }}" @endif
                    >
                        {{ $category->name }} ({{ __('common.ui.all') }})
                    </option>
                @else
                    <option 
                        value="{{ $category->id }}"
                        {{ ($selected == $category->id) ? 'selected' : '' }}
                    >
                        {{ $category->name }} ({{ __('common.ui.all') }})
                    </option>
                @endif

                @foreach($category->children as $child)
                    @if($attributes)
                        @php
                            $childAllAttrs = $child->getAllAttributes();
                            $childAllAttrs->each(function ($attr) {
                                if (
                                    $attr instanceof \App\Models\Attribute &&
                                    !$attr->relationLoaded('values')
                                ) {
                                    $attr->load('values');
                                }
                            });
                            $childAttributes = $childAllAttrs
                                ->filter(function ($attr) {
                                    return $attr instanceof \App\Models\Attribute;
                                })
                                ->map(function ($attr) {
                                    return [
                                        'id' => $attr->id,
                                        'name' => $attr->name,
                                        'slug' => $attr->slug,
                                        'type' => $attr->type->value,
                                        'is_required' => $attr->is_required,
                                        'values' => $attr->values
                                            ? $attr->values->pluck('value')->toArray()
                                            : [],
                                    ];
                                })
                                ->toJson();
                        @endphp
                        <option 
                            value="{{ $child->id }}"
                            {{ ($selected == $child->id) ? 'selected' : '' }}
                            data-attributes="{{ $childAttributes }}"
                        >
                            {{ $child->name }}
                        </option>
                    @else
                        <option 
                            value="{{ $child->id }}"
                            {{ ($selected == $child->id) ? 'selected' : '' }}
                        >
                            {{ $child->name }}
                        </option>
                    @endif
                @endforeach
            </optgroup>
        @else
            {{-- Category without children: Regular option --}}
            @if($attributes)
                @php
                    $allAttrs = $category->getAllAttributes();
                    $allAttrs->each(function ($attr) {
                        if (
                            $attr instanceof \App\Models\Attribute &&
                            !$attr->relationLoaded('values')
                        ) {
                            $attr->load('values');
                        }
                    });
                    $categoryAttributes = $allAttrs
                        ->filter(function ($attr) {
                            return $attr instanceof \App\Models\Attribute;
                        })
                        ->map(function ($attr) {
                            return [
                                'id' => $attr->id,
                                'name' => $attr->name,
                                'slug' => $attr->slug,
                                'type' => $attr->type->value,
                                'is_required' => $attr->is_required,
                                'values' => $attr->values
                                    ? $attr->values->pluck('value')->toArray()
                                    : [],
                            ];
                        })
                        ->toJson();
                @endphp
                <option 
                    value="{{ $category->id }}"
                    {{ ($selected == $category->id) ? 'selected' : '' }}
                    data-attributes="{{ $categoryAttributes }}"
                >
                    {{ $category->name }}
                </option>
            @else
                <option 
                    value="{{ $category->id }}"
                    {{ ($selected == $category->id) ? 'selected' : '' }}
                >
                    {{ $category->name }}
                </option>
            @endif
        @endif
    @endforeach
</select>
