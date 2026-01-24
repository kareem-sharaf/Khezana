@extends('layouts.app')

@section('title', $viewModel->title . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-item-detail-page">
        <div class="khezana-container">
            @include('public.items._partials.detail.breadcrumb', ['viewModel' => $viewModel])

            <div class="khezana-item-detail-layout">
                @include('public.items._partials.detail.images-enhanced', ['viewModel' => $viewModel])

                <div class="khezana-item-details">
                    @include('public.items._partials.detail.header', ['viewModel' => $viewModel])
                    @include('public.items._partials.detail.price', ['viewModel' => $viewModel])
                    @include('public.items._partials.detail.meta', ['viewModel' => $viewModel])
                    @include('public.items._partials.detail.attributes', ['viewModel' => $viewModel])
                    @include('public.items._partials.detail.description', ['viewModel' => $viewModel])
                    @include('public.items._partials.detail.cta', ['viewModel' => $viewModel])
                    @include('public.items._partials.detail.contact-form', ['viewModel' => $viewModel])
                    @include('public.items._partials.detail.additional-info', ['viewModel' => $viewModel])
                </div>
            </div>

            {{-- Similar Items Section --}}
            @if (isset($similarItems) && $similarItems->count() > 0)
                @include('public.items._partials.similar-items', ['items' => $similarItems])
            @endif
        </div>
    </div>

    @include('public.items._partials.detail.image-modal', ['viewModel' => $viewModel])
    @include('public.items._partials.detail.scripts-enhanced', ['viewModel' => $viewModel])
@endsection
