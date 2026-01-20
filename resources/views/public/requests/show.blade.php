@extends('layouts.app')

@section('meta')
    <meta name="robots" content="{{ $request->metaTags['robots'] ?? 'index, follow' }}">
    <meta property="og:type" content="{{ $request->metaTags['og:type'] ?? 'article' }}">
    <meta property="og:title" content="{{ $request->metaTags['og:title'] ?? $request->title }}">
    <meta property="og:description" content="{{ $request->metaTags['og:description'] ?? '' }}">
@endsection

@section('canonical')
    <link rel="canonical" href="{{ $request->canonicalUrl }}">
@endsection

@section('title', $request->title . ' - ' . config('app.name'))

@section('content')
    <x-container>
        <x-breadcrumbs :items="[
            ['label' => __('Home'), 'url' => route('home')],
            ['label' => $request->category->name ?? __('Requests'), 'url' => route('public.requests.index', $request->category ? ['category_id' => $request->category->id] : [])],
            ['label' => $request->title, 'url' => $request->url],
        ]" />
        
        <div class="request-page">
            <article class="request-details" itemscope itemtype="https://schema.org/Article">
                <h1 class="request-details__title" itemprop="headline">{{ $request->title }}</h1>
            
            <div class="request-details__meta">
                <x-shared.badge :type="$request->status" :label="$request->statusLabel" />
                @if($request->category)
                    <span class="request-details__category" itemprop="articleSection">
                        {{ $request->category->name }}
                    </span>
                @endif
            </div>
            
            <div class="request-details__description" itemprop="articleBody">
                {!! nl2br(e($request->description)) !!}
            </div>
            
            @if($request->attributes->isNotEmpty())
                <dl class="attributes-list" role="list">
                    @foreach($request->attributes as $attribute)
                        <div class="attributes-list__item" role="listitem">
                            <dt class="attributes-list__name">{{ $attribute->name }}:</dt>
                            <dd class="attributes-list__value">{{ $attribute->formattedValue }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
            
            @if($request->offers->isNotEmpty())
                <section class="request-details__offers" aria-label="{{ __('Offers') }}">
                    <h2>{{ __('Offers') }} ({{ $request->offers->count() }})</h2>
                    @foreach($request->offers as $offer)
                        <div class="offer-card">
                            <div class="offer-card__author">
                                <span>{{ $offer->user->name ?? __('Unknown') }}</span>
                                <time datetime="{{ $offer->createdAt->toIso8601String() }}">
                                    {{ $offer->createdAtFormatted }}
                                </time>
                            </div>
                            
                            <div class="offer-card__content">
                                @if($offer->item)
                                    <div class="offer-card__item">
                                        <a href="{{ $offer->item->url }}">{{ $offer->item->title }}</a>
                                    </div>
                                @endif
                                
                                <x-shared.badge 
                                    :type="$offer->operationType" 
                                    :label="$offer->operationTypeLabel" />
                                
                                @if($offer->price)
                                    <div class="offer-card__price">
                                        {{ number_format($offer->price, 2) }} {{ config('app.currency', 'SAR') }}
                                    </div>
                                @endif
                                
                                @if($offer->message)
                                    <p class="offer-card__message">{{ $offer->message }}</p>
                                @endif
                                
                                <x-shared.badge 
                                    :type="$offer->status" 
                                    :label="$offer->statusLabel" />
                            </div>
                        </div>
                    @endforeach
                </section>
            @elseif($request->offersCount > 0)
                <div class="request-details__offers-count">
                    {{ $request->offersCount }} {{ __('pending offers') }}
                </div>
            @endif
            
            @if($request->user)
                <div class="request-details__author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                    <span itemprop="name">{{ $request->user->name }}</span>
                    <time datetime="{{ $request->user->createdAt->toIso8601String() }}">
                        {{ $request->user->memberSinceFormatted }}
                    </time>
                </div>
            @endif
            
            <time datetime="{{ $request->createdAt->toIso8601String() }}" itemprop="datePublished" class="request-details__published">
                {{ __('Published') }}: {{ $request->createdAtFormatted }}
            </time>
            
                <div class="request-details__actions">
                    @guest
                        @if($request->status === 'open')
                            <form method="POST" action="{{ route('public.requests.offer', $request->id) }}" class="inline">
                                @csrf
                                <x-button type="primary" 
                                         data-tooltip="يجب تسجيل الدخول"
                                         title="يجب تسجيل الدخول">
                                    {{ __('Submit Offer') }}
                                </x-button>
                            </form>
                        @endif
                        
                        <form method="POST" action="{{ route('public.requests.contact', $request->id) }}" class="inline">
                            @csrf
                            <x-button type="secondary" 
                                     data-tooltip="يجب تسجيل الدخول"
                                     title="يجب تسجيل الدخول">
                                {{ __('Contact Requester') }}
                            </x-button>
                        </form>
                        
                        <form method="POST" action="{{ route('public.requests.report', $request->id) }}" class="inline">
                            @csrf
                            <x-button type="ghost" 
                                     data-tooltip="يجب تسجيل الدخول"
                                     title="يجب تسجيل الدخول">
                                {{ __('Report') }}
                            </x-button>
                        </form>
                    @else
                        @if($request->user->id !== auth()->id())
                            @if($request->status === 'open')
                                <form method="POST" action="{{ route('public.requests.offer', $request->id) }}" class="inline">
                                    @csrf
                                    <x-button type="primary">
                                        {{ __('Submit Offer') }}
                                    </x-button>
                                </form>
                            @endif
                            
                            <form method="POST" action="{{ route('public.requests.contact', $request->id) }}" class="inline">
                                @csrf
                                <x-button type="secondary">
                                    {{ __('Contact Requester') }}
                                </x-button>
                            </form>
                            
                            <form method="POST" action="{{ route('public.requests.report', $request->id) }}" class="inline">
                                @csrf
                                <x-button type="ghost">
                                    {{ __('Report') }}
                                </x-button>
                            </form>
                        @endif
                    @endguest
                </div>
            </article>
        </div>
    </x-container>
@endsection

