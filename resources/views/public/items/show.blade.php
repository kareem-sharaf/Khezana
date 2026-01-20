@extends('layouts.app')

@section('meta')
    <meta name="robots" content="{{ $item->metaTags['robots'] ?? 'index, follow' }}">
    <meta property="og:type" content="{{ $item->metaTags['og:type'] ?? 'product' }}">
    <meta property="og:title" content="{{ $item->metaTags['og:title'] ?? $item->title }}">
    <meta property="og:description" content="{{ $item->metaTags['og:description'] ?? '' }}">
    @if(isset($item->metaTags['og:image']))
        <meta property="og:image" content="{{ $item->metaTags['og:image'] }}">
    @endif
@endsection

@section('canonical')
    <link rel="canonical" href="{{ $item->canonicalUrl }}">
@endsection

@section('title', $item->title . ' - ' . config('app.name'))

@section('content')
    <div class="item-page">
        <article class="item-details" itemscope itemtype="https://schema.org/Product">
            <h1 class="item-details__title" itemprop="name">{{ $item->title }}</h1>
            
            <div class="item-details__meta">
                <x-shared.badge :type="$item->operationType" :label="$item->operationTypeLabel" />
                <x-shared.badge :type="$item->availabilityStatus" :label="$item->availabilityStatusLabel" />
                @if($item->category)
                    <span class="item-details__category" itemprop="category">
                        {{ $item->category->name }}
                    </span>
                @endif
            </div>
            
            @if($item->primaryImage)
                <div class="item-details__image">
                    <img src="{{ $item->primaryImage->path }}" 
                         alt="{{ $item->primaryImage->alt ?? $item->title }}"
                         loading="eager"
                         itemprop="image">
                </div>
            @endif
            
            @if($item->price)
                <div class="item-details__price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                    <span class="item-details__price-value" itemprop="price">{{ $item->getFormattedPrice() }}</span>
                    @if($item->depositAmount)
                        <span class="item-details__deposit">
                            {{ __('Deposit') }}: {{ number_format($item->depositAmount, 2) }} {{ config('app.currency', 'SAR') }}
                        </span>
                    @endif
                </div>
            @endif
            
            <div class="item-details__description" itemprop="description">
                {!! nl2br(e($item->description)) !!}
            </div>
            
            @if($item->attributes->isNotEmpty())
                <dl class="attributes-list" role="list">
                    @foreach($item->attributes as $attribute)
                        <div class="attributes-list__item" role="listitem">
                            <dt class="attributes-list__name">{{ $attribute->name }}:</dt>
                            <dd class="attributes-list__value">{{ $attribute->formattedValue }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
            
            @if($item->user)
                <div class="item-details__author" itemprop="seller" itemscope itemtype="https://schema.org/Person">
                    <span itemprop="name">{{ $item->user->name }}</span>
                    <time datetime="{{ $item->user->createdAt->toIso8601String() }}">
                        {{ $item->user->memberSinceFormatted }}
                    </time>
                </div>
            @endif
            
            <time datetime="{{ $item->createdAt->toIso8601String() }}" itemprop="datePublished" class="item-details__published">
                {{ __('Published') }}: {{ $item->createdAtFormatted }}
            </time>
        </article>
    </div>
@endsection
