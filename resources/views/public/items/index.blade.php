@extends('layouts.app')

@section('title', trans('items.plural') . ' - ' . config('app.name'))

@section('canonical')
    <link rel="canonical" href="{{ route('public.items.index') }}">
@endsection

@section('meta')
    <meta name="robots" content="index, follow">
@endsection

@section('content')
    <div class="items-page">
        <header class="items-page__header">
            <h1>{{ trans('items.plural') }}</h1>
        </header>
        
        @if($items->isEmpty())
            <x-shared.empty-state 
                :title="__('No items found')"
                :message="__('Try adjusting your filters or check back later.')" />
        @else
            <div class="items-page__list" role="list">
                @foreach($items as $item)
                    <x-items.item-card :item="$item" />
                @endforeach
            </div>
            
            <x-shared.pagination :paginator="$items" />
        @endif
    </div>
@endsection
