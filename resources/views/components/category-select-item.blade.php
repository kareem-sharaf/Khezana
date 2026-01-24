{{-- Recursive category item renderer --}}
@php
    $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
    $prefix = $level > 0 ? '└─ ' : '';
    
    if($attributes) {
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
    }
@endphp

<option 
    value="{{ $category->id }}"
    {{ ($selected == $category->id) ? 'selected' : '' }}
    @if($attributes) data-attributes="{{ $categoryAttributes }}" @endif
>
    {!! $indent !!}{!! $prefix !!}{{ $category->name }}
</option>

@php
    $hasChildren = false;
    if ($category->relationLoaded('children')) {
        $hasChildren = $category->children && $category->children->count() > 0;
    } else {
        // Fallback: check if category has children
        $hasChildren = $category->hasChildren();
    }
@endphp

@if($hasChildren)
    @php
        // Ensure children are loaded
        if (!$category->relationLoaded('children')) {
            $category->load(['children' => fn($q) => $q->where('is_active', true)->orderBy('name')]);
        }
    @endphp
    @foreach($category->children as $child)
        @include('components.category-select-item', [
            'category' => $child,
            'level' => $level + 1,
            'selected' => $selected,
            'attributes' => $attributes
        ])
    @endforeach
@endif
