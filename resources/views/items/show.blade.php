@extends('layouts.app')

@section('title', $viewModel->title . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-item-detail-page">
        <div class="khezana-container">
            @include('items._partials.detail.breadcrumb', ['viewModel' => $viewModel])

            <div class="khezana-item-detail-layout">
                @include('items._partials.detail.images', ['viewModel' => $viewModel])

                <div class="khezana-item-details">
                    @include('items._partials.detail.header', ['viewModel' => $viewModel])
                    @include('items._partials.detail.price', ['viewModel' => $viewModel])
                    @include('items._partials.detail.meta', ['viewModel' => $viewModel])
                    @include('items._partials.detail.attributes', ['viewModel' => $viewModel])
                    @include('items._partials.detail.description', ['viewModel' => $viewModel])
                    @include('items._partials.detail.approval-info', ['viewModel' => $viewModel])
                    @include('items._partials.detail.actions', ['viewModel' => $viewModel])
                    @include('items._partials.detail.additional-info', ['viewModel' => $viewModel])
                </div>
            </div>
        </div>
    </div>

    @include('items._partials.detail.delete-modal', ['viewModel' => $viewModel])
    @include('items._partials.detail.scripts', ['viewModel' => $viewModel])
@endsection
