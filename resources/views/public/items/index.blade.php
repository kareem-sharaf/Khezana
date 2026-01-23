@extends('layouts.app')

@section('title', __('items.title') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            @include('public.items._partials.page-header', ['items' => $items])

            <main class="khezana-listing-main" role="main">
                @if ($items->count() > 0)
                    @include('public.items._partials.grid', ['items' => $items])
                    @include('public.items._partials.pagination', ['items' => $items])
                @else
                    @include('public.items._partials.empty-state')
                @endif
            </main>
        </div>
    </div>
@endsection
