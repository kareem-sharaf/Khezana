@props([
    'filters' => [],
    'categories' => [],
])

<div class="filters">
    <form method="GET" action="{{ route('public.requests.index') }}" class="filters__form">
        <div class="filters__group">
            <label class="filters__label">{{ __('Status') }}</label>
            <select name="status" class="filters__select">
                <option value="">{{ __('All') }}</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
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

        @if(request()->hasAny(['status', 'category_id']))
            <a href="{{ route('public.requests.index') }}" class="filters__clear">
                {{ __('Clear Filters') }}
            </a>
        @endif

        <button type="submit" class="btn btn-primary btn-small">
            {{ __('Apply Filters') }}
        </button>
    </form>
</div>
