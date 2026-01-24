@extends('layouts.app')

@section('title', __('items.title') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            @if(session('success'))
                <x-alert 
                    type="success" 
                    :message="session('success')" 
                    dismissible="true"
                    class="khezana-mb-md" />
            @endif

            <x-breadcrumb :items="[['label' => __('items.title'), 'url' => null]]" />

            @include('public.items._partials.page-header', ['items' => $items])

            <div class="khezana-listing-layout">
                {{-- Filters Sidebar --}}
                <aside class="khezana-listing-layout__sidebar">
                    @include('public.items._partials.filters', [
                        'filters' => $filters ?? [],
                        'categories' => $categories ?? collect(),
                        'activeFiltersCount' => $activeFiltersCount ?? 0,
                    ])
                </aside>

                {{-- Main Content --}}
                <main class="khezana-listing-layout__main" role="main">
                    @include('public.items._partials.filter-chips', [
                        'filters' => $filters ?? [],
                        'categories' => $categories ?? collect(),
                    ])
                    @if ($items->count() > 0)
                        @include('public.items._partials.grid', ['items' => $items])
                        @include('public.items._partials.pagination', ['items' => $items])
                    @else
                        @include('public.items._partials.empty-state')
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection
