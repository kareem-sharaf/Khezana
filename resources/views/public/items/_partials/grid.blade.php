{{-- Public Items Grid Partial --}}
{{-- Usage: @include('public.items._partials.grid', ['items' => $items]) --}}

@php
    use App\Helpers\ItemCardHelper;
@endphp

<div class="khezana-items-grid-modern" role="list">
    @foreach ($items as $item)
        <div role="listitem">
            @include('partials.item-card', array_merge(
                ['item' => $item],
                ItemCardHelper::preparePublicItem($item)
            ))
        </div>
    @endforeach
</div>
