@props([
    'items' => [],
])

@if(!empty($items))
    <nav class="breadcrumbs" aria-label="{{ __('Breadcrumb') }}">
        <ol class="breadcrumbs__list">
            @foreach($items as $index => $item)
                <li class="breadcrumbs__item">
                    @if($index < count($items) - 1)
                        <a href="{{ $item['url'] }}" class="breadcrumbs__link">
                            {{ $item['label'] }}
                        </a>
                        <span class="breadcrumbs__separator" aria-hidden="true">›</span>
                    @else
                        <span class="breadcrumbs__current" aria-current="page">
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
