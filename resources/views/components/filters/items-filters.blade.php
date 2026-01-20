@props([
    'filters' => [],
    'categories' => [],
])

<div class="filters">
    <form method="GET" action="{{ route('public.items.index') }}" class="filters__form">
        <div class="filters__group">
            <label class="filters__label">{{ __('Operation Type') }}</label>
            <select name="operation_type" class="filters__select">
                <option value="">{{ __('All') }}</option>
                <option value="sell" {{ request('operation_type') === 'sell' ? 'selected' : '' }}>{{ __('Sell') }}</option>
                <option value="rent" {{ request('operation_type') === 'rent' ? 'selected' : '' }}>{{ __('Rent') }}</option>
                <option value="donate" {{ request('operation_type') === 'donate' ? 'selected' : '' }}>{{ __('Donate') }}</option>
            </select>
        </div>

        <div class="filters__group">
            <label class="filters__label">{{ __('Category') }}</label>
            <select name="category_id" class="filters__select">
                <option value="">{{ __('All Categories') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filters__group">
            <label class="filters__label">{{ __('Price Range') }}</label>
            <div class="filters__price-range">
                <input type="number" 
                       name="price_min" 
                       placeholder="{{ __('Min') }}"
                       value="{{ request('price_min') }}"
                       class="filters__input"
                       min="0"
                       step="0.01">
                <span class="filters__separator">-</span>
                <input type="number" 
                       name="price_max" 
                       placeholder="{{ __('Max') }}"
                       value="{{ request('price_max') }}"
                       class="filters__input"
                       min="0"
                       step="0.01">
            </div>
        </div>

        @if(request()->hasAny(['operation_type', 'category_id', 'price_min', 'price_max']))
            <a href="{{ route('public.items.index') }}" class="filters__clear">
                {{ __('Clear Filters') }}
            </a>
        @endif

        <button type="submit" class="btn btn-primary btn-small">
            {{ __('Apply Filters') }}
        </button>
    </form>
</div>
