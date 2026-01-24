{{-- Similar Items Section --}}
{{-- Usage: @include('public.items._partials.similar-items', ['items' => $similarItems]) --}}

@php
    use App\Helpers\ItemCardHelper;
@endphp

@if ($items && $items->count() > 0)
    <section class="khezana-similar-items" aria-label="{{ __('items.similar_items') }}">
        <div class="khezana-container">
            <div class="khezana-similar-items__header">
                <h2 class="khezana-similar-items__title">
                    {{ __('items.similar_items') }}
                </h2>
                <p class="khezana-similar-items__subtitle">
                    {{ __('items.similar_items_description') }}
                </p>
            </div>

            <div class="khezana-items-grid-modern" role="list">
                @foreach ($items as $item)
                    <div role="listitem">
                        @include(
                            'partials.item-card',
                            array_merge(['item' => $item], ItemCardHelper::preparePublicItem($item)))
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
