@props([
    'request',
    'showCategory' => true,
    'showOffersCount' => true,
    'showDate' => true,
])

<article class="request-card" itemscope itemtype="https://schema.org/Article">
    <a href="{{ $request->url }}" class="request-card__link">
        <div class="request-card__content">
            <div class="request-card__header">
                <x-shared.badge type="request">{{ __('Request') }}</x-shared.badge>
                <x-shared.badge 
                    :type="$request->status" 
                    :label="$request->statusLabel" />
            </div>
            
            <h3 class="request-card__title" itemprop="headline">
                {{ $request->title }}
            </h3>
            
            <p class="request-card__description" itemprop="description">
                {{ Str::limit($request->description, 120) }}
            </p>
            
            @if($showCategory && $request->category)
                <div class="request-card__category" itemprop="articleSection">
                    {{ $request->category->name }}
                </div>
            @endif
            
            @if($showOffersCount && $request->offersCount > 0)
                <div class="request-card__offers">
                    {{ __('Offers') }}: {{ $request->offersCount }}
                </div>
            @endif
            
            @if($showDate)
                <div class="request-card__date">
                    <time datetime="{{ $request->createdAt->toIso8601String() }}" itemprop="datePublished">
                        {{ $request->createdAt->diffForHumans() }}
                    </time>
                </div>
            @endif
            
            <div class="request-card__author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                <span itemprop="name">{{ $request->user->name ?? __('Unknown') }}</span>
            </div>
        </div>
    </a>
</article>

