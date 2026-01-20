@props(['request'])

<article class="request-card" itemscope itemtype="https://schema.org/Article">
    <div class="request-card__content">
        <h2 class="request-card__title">
            <a href="{{ $request->url }}" itemprop="headline">{{ $request->title }}</a>
        </h2>
        
        <p class="request-card__description" itemprop="description">
            {{ Str::limit($request->description, 150) }}
        </p>
        
        @if($request->category)
            <span class="request-card__category" itemprop="articleSection">
                {{ $request->category->name }}
            </span>
        @endif
        
        <x-shared.badge 
            :type="$request->status" 
            :label="$request->statusLabel" />
        
        @if($request->offersCount > 0)
            <span class="request-card__offers-count">
                {{ $request->offersCount }} {{ __('offers') }}
            </span>
        @endif
        
        <div class="request-card__meta">
            <span class="request-card__author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                <span itemprop="name">{{ $request->user->name ?? __('Unknown') }}</span>
            </span>
            <time datetime="{{ $request->createdAt->toIso8601String() }}" itemprop="datePublished">
                {{ $request->createdAtFormatted }}
            </time>
        </div>
    </div>
</article>
