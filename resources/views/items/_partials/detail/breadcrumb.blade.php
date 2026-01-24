{{-- Breadcrumb Partial --}}
{{-- Usage: @include('items._partials.detail.breadcrumb', ['viewModel' => $viewModel]) --}}

@php
    $breadcrumbItems = [];
    foreach ($viewModel->breadcrumbs as $breadcrumb) {
        $breadcrumbItems[] = [
            'label' => $breadcrumb['label'],
            'url' => $breadcrumb['url'] ?? null,
        ];
    }
@endphp

<x-breadcrumb :items="$breadcrumbItems" />
