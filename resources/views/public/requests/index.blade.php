@extends('layouts.app')

@section('title', __('requests.title') . ' - ' . config('app.name'))

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

            <x-breadcrumb :items="[['label' => __('requests.title'), 'url' => null]]" />

            @include('public.requests._partials.page-header', ['requests' => $requests])

            <div class="khezana-listing-layout">
                {{-- Filters Sidebar --}}
                <aside class="khezana-listing-layout__sidebar">
                    @include('public.requests._partials.filters', [
                        'filters' => $filters ?? [],
                        'categories' => $categories ?? collect(),
                        'activeFiltersCount' => $activeFiltersCount ?? 0,
                    ])
                </aside>

                {{-- Main Content --}}
                <main class="khezana-listing-layout__main" role="main">
                    @include('public.requests._partials.filter-chips', [
                        'filters' => $filters ?? [],
                        'categories' => $categories ?? collect(),
                    ])
                    @if (isset($requests) && $requests->total() > 0)
                        @include('public.requests._partials.grid', ['requests' => $requests])
                        @include('public.requests._partials.pagination', ['requests' => $requests])
                    @else
                        @include('public.requests._partials.empty-state')
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection
