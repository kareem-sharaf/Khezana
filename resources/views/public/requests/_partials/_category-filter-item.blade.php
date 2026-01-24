{{-- Recursive category filter item renderer --}}
@php
    $hasChildren = false;
    if ($category->relationLoaded('children')) {
        $hasChildren = $category->children && $category->children->count() > 0;
    } else {
        $hasChildren = $category->hasChildren();
    }
    $isSelected = isset($currentFilters['category_id']) && (int) $currentFilters['category_id'] === $category->id;
@endphp

<label class="khezana-filter-list__item {{ $level > 0 ? 'khezana-filter-list__item--child' : '' }}"
    data-level="{{ $level }}">
    <input type="radio" name="category_id" value="{{ $category->id }}" {{ $isSelected ? 'checked' : '' }}>
    <span>{{ $category->name }}</span>
    @if ($hasChildren)
        <svg class="khezana-filter-list__item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12"
            height="12" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    @endif
</label>

@if ($hasChildren)
    @php
        // Ensure children are loaded
        if (!$category->relationLoaded('children')) {
            $category->load(['children' => fn($q) => $q->where('is_active', true)->orderBy('name')]);
        }
    @endphp
    @foreach ($category->children as $child)
        @include('public.requests._partials._category-filter-item', [
            'category' => $child,
            'level' => $level + 1,
            'currentFilters' => $currentFilters,
        ])
    @endforeach
@endif
