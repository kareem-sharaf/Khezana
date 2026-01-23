@extends('layouts.app')

@section('title', __('requests.title') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            @include('public.requests._partials.page-header', ['requests' => $requests])

            <main class="khezana-listing-main" role="main">
                @if ($requests->count() > 0)
                    @include('public.requests._partials.grid', ['requests' => $requests])
                    @include('public.requests._partials.pagination', ['requests' => $requests])
                @else
                    @include('public.requests._partials.empty-state')
                @endif
            </main>
        </div>
    </div>
@endsection
