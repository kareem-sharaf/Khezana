@extends('layouts.app')

@section('title', trans('requests.plural') . ' - ' . config('app.name'))

@section('canonical')
    <link rel="canonical" href="{{ route('public.requests.index') }}">
@endsection

@section('meta')
    <meta name="robots" content="index, follow">
@endsection

@section('content')
    <div class="requests-page">
        <header class="requests-page__header">
            <h1>{{ trans('requests.plural') }}</h1>
        </header>
        
        @if($requests->isEmpty())
            <x-shared.empty-state 
                :title="__('No requests found')"
                :message="__('Try adjusting your filters or check back later.')" />
        @else
            <div class="requests-page__list" role="list">
                @foreach($requests as $request)
                    <x-requests.request-card :request="$request" />
                @endforeach
            </div>
            
            <x-shared.pagination :paginator="$requests" />
        @endif
    </div>
@endsection
