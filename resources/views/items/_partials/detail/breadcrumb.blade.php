{{-- Breadcrumb Partial --}}
{{-- Usage: @include('items._partials.detail.breadcrumb', ['viewModel' => $viewModel]) --}}

<nav class="khezana-breadcrumb">
    @foreach ($viewModel->breadcrumbs as $breadcrumb)
        @if ($breadcrumb['url'])
            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
            <span>/</span>
        @else
            <span>{{ $breadcrumb['label'] }}</span>
        @endif
    @endforeach
</nav>

