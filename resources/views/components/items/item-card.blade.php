@props(['item'])

<article class="item-card" itemscope itemtype="https://schema.org/Product">
    <a href="{{ $item->url }}" class="item-card__image">
        @if($item->primaryImage)
            <img src="{{ $item->primaryImage->path }}" 
                 alt="{{ $item->primaryImage->alt ?? $item->title }}"
                 loading="lazy"
                 itemprop="image">
        @else
            <div class="item-card__placeholder">{{ __('No Image') }}</div>
        @endif
    </a>
    
    <div class="item-card__content">
        <h2 class="item-card__title">
            <a href="{{ $item->url }}" itemprop="name">{{ $item->title }}</a>
        </h2>
        
        @if($item->category)
            <span class="item-card__category" itemprop="category">
                {{ $item->category->name }}
            </span>
        @endif
        
        @if($item->price)
            <div class="item-card__price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                <span itemprop="price">{{ $item->getFormattedPrice() }}</span>
            </div>
        @endif
        
        <x-shared.badge 
            :type="$item->operationType" 
            :label="$item->operationTypeLabel" />
        
        <x-shared.badge 
            :type="$item->availabilityStatus" 
            :label="$item->availabilityStatusLabel" />
        
        <div class="item-card__meta">
            <span class="item-card__author" itemprop="seller" itemscope itemtype="https://schema.org/Person">
                <span itemprop="name">{{ $item->user->name ?? __('Unknown') }}</span>
            </span>
            <time datetime="{{ $item->createdAt->toIso8601String() }}" itemprop="datePublished">
                {{ $item->createdAtFormatted }}
            </time>
        </div>
    </div>
</article>
