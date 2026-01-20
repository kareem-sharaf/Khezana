@extends('layouts.app')

@section('title', trans('items.plural') . ' - ' . config('app.name'))

@section('canonical')
    <link rel="canonical" href="{{ route('public.items.index') }}">
@endsection

@section('meta')
    <meta name="robots" content="index, follow">
@endsection

@section('content')
    <x-container>
        <div class="items-page">
            <header class="items-page__header">
                <h1 class="items-page__title">{{ trans('items.plural') }}</h1>
                
                <div class="items-page__search">
                    <x-search-bar 
                        :placeholder="__('Search items...')"
                        :action="route('public.items.index')"
                        :value="request('search')" />
                </div>
                
                <div class="items-page__sort">
                    <form method="GET" action="{{ route('public.items.index') }}" class="sort-form">
                        @foreach(request()->except('sort', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <select name="sort" 
                                class="sort-form__select"
                                onchange="this.form.submit()">
                            <option value="created_at_desc" {{ request('sort') === 'created_at_desc' ? 'selected' : '' }}>
                                {{ __('Latest') }}
                            </option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>
                                {{ __('Price: Low to High') }}
                            </option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>
                                {{ __('Price: High to Low') }}
                            </option>
                            <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>
                                {{ __('Title: A-Z') }}
                            </option>
                            <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>
                                {{ __('Title: Z-A') }}
                            </option>
                        </select>
                    </form>
                </div>
            </header>
            
            <div class="items-page__layout">
                <aside class="items-page__sidebar">
                    <button class="filters__toggle" 
                            onclick="document.querySelector('.items-page__sidebar').classList.toggle('is-open')">
                        {{ __('Filters') }}
                    </button>
                    <x-filters.items-filters :filters="$filters" :categories="\App\Models\Category::active()->get()" />
                </aside>
                
                <main class="items-page__main">
                    @if(request()->hasAny(['search', 'operation_type', 'category_id', 'price_min', 'price_max']))
                        <div class="items-page__active-filters">
                            <span class="active-filters__label">{{ __('Active Filters') }}:</span>
                            @if(request('search'))
                                <span class="active-filters__chip">
                                    {{ __('Search') }}: "{{ request('search') }}"
                                    <a href="{{ route('public.items.index', request()->except('search', 'page')) }}" class="active-filters__remove">×</a>
                                </span>
                            @endif
                            @if(request('operation_type'))
                                <span class="active-filters__chip">
                                    {{ __('Operation') }}: {{ ucfirst(request('operation_type')) }}
                                    <a href="{{ route('public.items.index', request()->except('operation_type', 'page')) }}" class="active-filters__remove">×</a>
                                </span>
                            @endif
                            @if(request('category_id'))
                                @php
                                    $category = \App\Models\Category::find(request('category_id'));
                                @endphp
                                @if($category)
                                    <span class="active-filters__chip">
                                        {{ __('Category') }}: {{ $category->name }}
                                        <a href="{{ route('public.items.index', request()->except('category_id', 'page')) }}" class="active-filters__remove">×</a>
                                    </span>
                                @endif
                            @endif
                            @if(request('price_min') || request('price_max'))
                                <span class="active-filters__chip">
                                    {{ __('Price') }}: {{ request('price_min', 0) }} - {{ request('price_max', '∞') }}
                                    <a href="{{ route('public.items.index', request()->except('price_min', 'price_max', 'page')) }}" class="active-filters__remove">×</a>
                                </span>
                            @endif
                            <a href="{{ route('public.items.index') }}" class="active-filters__clear">
                                {{ __('Clear All') }}
                            </a>
                        </div>
                    @endif
                    
                    @if($items->isEmpty())
                        <x-shared.empty-state 
                            icon="📦"
                            :title="__('No items found')"
                            :message="__('Try adjusting your filters or check back later.')" />
                    @else
                        <div class="items-page__grid" role="list">
                            @foreach($items as $item)
                                <x-items.item-card :item="$item" />
                            @endforeach
                        </div>
                        
                        <x-shared.pagination :paginator="$items" />
                    @endif
                </main>
            </div>
        </div>
    </x-container>
@endsection

