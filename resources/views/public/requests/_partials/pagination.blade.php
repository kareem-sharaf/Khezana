{{-- Pagination Partial for Public Requests --}}
{{-- Usage: @include('public.requests._partials.pagination', ['requests' => $requests]) --}}

@if ($requests->hasPages())
    <nav class="khezana-pagination" aria-label="{{ __('common.ui.pagination') }}">
        {{ $requests->appends(request()->query())->links() }}
    </nav>
@endif
