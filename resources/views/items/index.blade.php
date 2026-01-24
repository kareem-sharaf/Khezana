@extends('layouts.app')

@section('title', __('common.ui.my_items_page') . ' - ' . config('app.name'))

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

            @include('items._partials.page-header', [
                'items' => $items,
                'showCreateButton' => true,
            ])

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
                    @if ($items->count() > 0)
                        @include('items._partials.grid', ['items' => $items])
                        @include('items._partials.pagination', ['items' => $items])
                    @else
                        @include('items._partials.empty-state', [
                            'type' => 'user',
                        ])
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection
