@extends('layouts.app')

@section('title', trans('requests.plural') . ' - ' . config('app.name'))

@section('canonical')
    <link rel="canonical" href="{{ route('public.requests.index') }}">
@endsection

@section('meta')
    <meta name="robots" content="index, follow">
@endsection

@section('content')
    <x-container>
        <div class="requests-page">
            <header class="requests-page__header">
                <h1 class="requests-page__title">{{ trans('requests.plural') }}</h1>
                
                <div class="requests-page__search">
                    <x-search-bar 
                        :placeholder="__('Search requests...')"
                        :action="route('public.requests.index')"
                        :value="request('search')" />
                </div>
                
                <div class="requests-page__sort">
                    <form method="GET" action="{{ route('public.requests.index') }}" class="sort-form">
                        @foreach(request()->except('sort', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <select name="sort" 
                                class="sort-form__select"
                                onchange="this.form.submit()">
                            <option value="created_at_desc" {{ request('sort') === 'created_at_desc' ? 'selected' : '' }}>
                                {{ __('Latest') }}
                            </option>
                            <option value="status_asc" {{ request('sort') === 'status_asc' ? 'selected' : '' }}>
                                {{ __('Status: A-Z') }}
                            </option>
                            <option value="status_desc" {{ request('sort') === 'status_desc' ? 'selected' : '' }}>
                                {{ __('Status: Z-A') }}
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
            
            <div class="requests-page__layout">
                <aside class="requests-page__sidebar">
                    <button class="filters__toggle" 
                            onclick="document.querySelector('.requests-page__sidebar').classList.toggle('is-open')">
                        {{ __('Filters') }}
                    </button>
                    <x-filters.requests-filters :filters="$filters" :categories="\App\Models\Category::active()->get()" />
                </aside>
                
                <main class="requests-page__main">
                    @if(request()->hasAny(['search', 'status', 'category_id']))
                        <div class="requests-page__active-filters">
                            <span class="active-filters__label">{{ __('Active Filters') }}:</span>
                            @if(request('search'))
                                <span class="active-filters__chip">
                                    {{ __('Search') }}: "{{ request('search') }}"
                                    <a href="{{ route('public.requests.index', request()->except('search', 'page')) }}" class="active-filters__remove">×</a>
                                </span>
                            @endif
                            @if(request('status'))
                                <span class="active-filters__chip">
                                    {{ __('Status') }}: {{ ucfirst(request('status')) }}
                                    <a href="{{ route('public.requests.index', request()->except('status', 'page')) }}" class="active-filters__remove">×</a>
                                </span>
                            @endif
                            @if(request('category_id'))
                                @php
                                    $category = \App\Models\Category::find(request('category_id'));
                                @endphp
                                @if($category)
                                    <span class="active-filters__chip">
                                        {{ __('Category') }}: {{ $category->name }}
                                        <a href="{{ route('public.requests.index', request()->except('category_id', 'page')) }}" class="active-filters__remove">×</a>
                                    </span>
                                @endif
                            @endif
                            <a href="{{ route('public.requests.index') }}" class="active-filters__clear">
                                {{ __('Clear All') }}
                            </a>
                        </div>
                    @endif
                    
                    @if($requests->isEmpty())
                        <x-shared.empty-state 
                            icon="📋"
                            :title="__('No requests found')"
                            :message="__('Try adjusting your filters or check back later.')" />
                    @else
                        <div class="requests-page__grid" role="list">
                            @foreach($requests as $request)
                                <x-requests.request-card :request="$request" />
                            @endforeach
                        </div>
                        
                        <x-shared.pagination :paginator="$requests" />
                    @endif
                </main>
            </div>
        </div>
    </x-container>
@endsection

