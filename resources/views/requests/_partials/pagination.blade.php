{{-- Pagination Partial for Requests --}}
{{-- Usage: @include('requests._partials.pagination', ['requests' => $requests]) --}}

@if ($requests->hasPages())
    <nav class="khezana-pagination" aria-label="{{ __('common.ui.pagination') }}">
        {{ $requests->appends(request()->query())->links() }}
    </nav>
@endif
