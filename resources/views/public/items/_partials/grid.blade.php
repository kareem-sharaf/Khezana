{{-- Public Items Grid Partial --}}
{{-- Usage: @include('public.items._partials.grid', ['items' => $items]) --}}

<div class="khezana-items-grid-modern" role="list">
    @foreach ($items as $item)
        <div role="listitem">
            @include('partials.item-card', ['item' => $item])
        </div>
    @endforeach
</div>
