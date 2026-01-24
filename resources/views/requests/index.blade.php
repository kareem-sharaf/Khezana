@extends('layouts.app')

@section('title', __('common.ui.my_requests_page') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            <x-breadcrumb :items="[['label' => __('common.ui.my_requests_page'), 'url' => null]]" />

            @include('requests._partials.page-header', [
                'requests' => $requests,
                'showCreateButton' => true,
            ])

            <main class="khezana-listing-main" role="main">
                @if ($requests->count() > 0)
                    @include('requests._partials.grid', ['requests' => $requests])
                    @include('requests._partials.pagination', ['requests' => $requests])
                @else
                    @include('requests._partials.empty-state', [
                        'type' => 'user',
                    ])
                @endif
            </main>
        </div>
    </div>
@endsection
