@extends('layouts.app')

@section('title', __('items.title') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            @include('public.items._partials.page-header', ['items' => $items])

            <div class="khezana-listing-layout">
                {{-- Filters Sidebar --}}
                <aside class="khezana-listing-layout__sidebar">
                    @include('public.items._partials.filters', [
                        'filters' => $filters ?? [],
                        'categories' => $categories ?? collect(),
                        'filterRoute' => route('public.items.index'),
                    ])
                </aside>

                {{-- Main Content --}}
                <main class="khezana-listing-layout__main" role="main">
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
