@props([
    'item',
    'showCategory' => true,
    'showLocation' => false,
    'showDate' => true,
])

<article class="item-card" itemscope itemtype="https://schema.org/Product">
    <a href="{{ $item->url }}" class="item-card__link">
        <div class="item-card__image">
            @if($item->primaryImage)
                <img src="{{ $item->primaryImage->path }}" 
                     alt="{{ $item->primaryImage->alt ?? $item->title }}"
                     loading="lazy"
                     itemprop="image">
            @else
                <div class="item-card__placeholder">{{ __('No Image') }}</div>
            @endif
        </div>
        
        <div class="item-card__content">
            <div class="item-card__badge">
                <x-shared.badge 
                    :type="$item->operationType" 
                    :label="$item->operationTypeLabel" />
            </div>
            
            <h3 class="item-card__title" itemprop="name">
                {{ $item->title }}
            </h3>
            
            @if($item->price)
                <div class="item-card__price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                    <span class="item-card__price-value" itemprop="price">
                        {{ $item->getFormattedPrice() }}
                    </span>
                </div>
            @endif
            
            @if($showCategory && $item->category)
                <div class="item-card__category" itemprop="category">
                    {{ $item->category->name }}
                </div>
            @endif
            
            @if($showDate)
                <div class="item-card__date">
                    <time datetime="{{ $item->createdAt->toIso8601String() }}" itemprop="datePublished">
                        {{ $item->createdAt->diffForHumans() }}
                    </time>
                </div>
            @endif
            
            <div class="item-card__author" itemprop="seller" itemscope itemtype="https://schema.org/Person">
                <span itemprop="name">{{ $item->user->name ?? __('Unknown') }}</span>
            </div>
        </div>
    </a>
</article>

