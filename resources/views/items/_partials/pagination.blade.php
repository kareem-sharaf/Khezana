{{-- Pagination Partial --}}
{{-- Usage: @include('items._partials.pagination', ['items' => $items]) --}}

@if ($items->hasPages())
    <nav class="khezana-pagination" aria-label="{{ __('common.ui.pagination') }}">
        {{ $items->appends(request()->query())->links() }}
    </nav>
@endif
