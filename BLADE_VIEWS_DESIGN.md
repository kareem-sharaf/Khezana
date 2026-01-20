# 🎨 Blade Views Architecture - Khezana Marketplace
## تصميم واجهات Blade - SSR & Components-Based Design

**الإصدار:** 1.0  
**التاريخ:** 2026-01-20  
**الغرض:** Blueprint نهائي لتنفيذ Blade Views وفق SSR و Components-Based Design  
**المرجع الإلزامي:** هذا المستند هو المرجع الوحيد لأي تنفيذ Views لاحقاً

---

## 📌 جدول المحتويات

1. [Overview - نظرة عامة](#1-overview)
2. [Folder Structure - هيكل المجلدات](#2-folder-structure)
3. [Base Layout - التخطيط الأساسي](#3-base-layout)
4. [Reusable Components - المكونات القابلة لإعادة الاستخدام](#4-reusable-components)
5. [Empty State Design - تصميم الحالات الفارغة](#5-empty-state-design)
6. [SEO Integration - تكامل SEO](#6-seo-integration)
7. [Performance Rules - قواعد الأداء](#7-performance-rules)

---

## 1. Overview - نظرة عامة

### 1.1 الهدف

**Blade Views Architecture** هو تصميم واجهات Blade مخصصة للعرض العام (Public Read Flow) مع التركيز على:
- Server-Side Rendering (SSR)
- Components-Based Design
- SEO Optimization
- Performance (Low JS, Fast Loading)
- Accessibility

### 1.2 المبادئ الأساسية

- ✅ **Blade Only**: لا Vue، لا React، Blade فقط
- ✅ **SSR First**: Server-Side Rendering أولوية
- ✅ **Minimal JavaScript**: JavaScript فقط للضرورة القصوى (Progressive Enhancement)
- ✅ **Components-Based**: تصميم قائم على Components
- ✅ **SEO-Friendly**: Meta tags، Canonical URLs، Semantic HTML
- ✅ **Accessibility**: ARIA labels، Semantic HTML، Keyboard navigation
- ✅ **Performance**: Lazy loading، Minimal assets، Fast rendering

### 1.3 الفصل عن Write Layer

**⚠️ قاعدة إلزامية:**
- Views **لا تحتوي** على Forms للكتابة (Create/Update/Delete)
- Views **لا تحتوي** على Business Logic
- Views **تعتمد فقط** على Read Models من Controllers
- Views **لا تستخدم** Policies أو Guards مباشرة

### 1.4 المرجع الإلزامي

**هذا التصميم مبني بالكامل على:**
- `CONTROLLERS_LAYER_DESIGN.md` - View Contracts و Variables
- `PUBLIC_READ_FLOW.md` - SEO Rules و URL Structure
- `DESIGN_SYSTEM.md` - Design Principles (للإشارة)

---

## 2. Folder Structure - هيكل المجلدات

### 2.1 الهيكل المقترح

```
resources/
└── views/
    ├── layouts/
    │   ├── app.blade.php              # Base layout (Public)
    │   └── user.blade.php             # User context layout
    │
    ├── components/
    │   ├── items/
    │   │   ├── item-card.blade.php    # Item card component
    │   │   └── item-details.blade.php # Item details component
    │   │
    │   ├── requests/
    │   │   ├── request-card.blade.php # Request card component
    │   │   └── request-details.blade.php # Request details component
    │   │
    │   ├── offers/
    │   │   └── offer-card.blade.php   # Offer card component
    │   │
    │   ├── shared/
    │   │   ├── empty-state.blade.php  # Empty state component
    │   │   ├── pagination.blade.php   # Pagination component
    │   │   ├── badge.blade.php        # Badge component (status/availability)
    │   │   ├── image-gallery.blade.php # Image gallery component
    │   │   ├── attributes-list.blade.php # Attributes list component
    │   │   └── breadcrumbs.blade.php  # Breadcrumbs component
    │   │
    │   └── seo/
    │       ├── meta-tags.blade.php     # Meta tags component
    │       └── canonical.blade.php     # Canonical URL component
    │
    ├── partials/
    │   ├── header.blade.php            # Site header
    │   ├── footer.blade.php            # Site footer
    │   ├── navigation.blade.php        # Main navigation
    │   └── filters.blade.php           # Filters sidebar (optional)
    │
    ├── public/
    │   ├── items/
    │   │   ├── index.blade.php        # Browse Items
    │   │   └── show.blade.php         # View Item Details
    │   │
    │   ├── requests/
    │   │   ├── index.blade.php        # Browse Requests
    │   │   └── show.blade.php         # View Request Details
    │   │
    │   └── search/
    │       ├── items.blade.php         # Search Items Results
    │       └── requests.blade.php      # Search Requests Results
    │
    └── user/
        ├── items/
        │   └── index.blade.php        # User's Items (Read-Only)
        │
        └── requests/
            ├── index.blade.php         # User's Requests (Read-Only)
            └── offers.blade.php        # View Offers for Request
```

### 2.2 مسؤولية كل Folder

#### 2.2.1 `resources/views/layouts/`

**المسؤولية:**
- Base layouts للصفحات
- HTML5 semantic structure
- SEO meta tags structure
- Yield sections واضحة

**المحتوى:**
- `app.blade.php` - Base layout للصفحات العامة
- `user.blade.php` - Layout للصفحات الخاصة بالمستخدم (مع Authentication)

---

#### 2.2.2 `resources/views/components/`

**المسؤولية:**
- Reusable Blade components
- Components قابلة لإعادة الاستخدام عبر الصفحات
- Props واضحة ومحددة

**المحتوى:**
- `items/` - Item-related components
- `requests/` - Request-related components
- `offers/` - Offer-related components
- `shared/` - Shared components (EmptyState, Pagination, Badge, etc.)
- `seo/` - SEO-related components (MetaTags, Canonical)

---

#### 2.2.3 `resources/views/partials/`

**المسؤولية:**
- Partial views (Header, Footer, Navigation)
- Shared UI elements
- Site-wide components

**المحتوى:**
- `header.blade.php` - Site header
- `footer.blade.php` - Site footer
- `navigation.blade.php` - Main navigation menu
- `filters.blade.php` - Filters sidebar (optional)

---

#### 2.2.4 `resources/views/public/`

**المسؤولية:**
- Public pages (Browse, View, Search)
- متاحة للـ Guests والمستخدمين المسجلين

**المحتوى:**
- `items/` - Item pages (Browse, View)
- `requests/` - Request pages (Browse, View)
- `search/` - Search results pages

---

#### 2.2.5 `resources/views/user/`

**المسؤولية:**
- User context pages (Read-Only)
- تتطلب Authentication
- عرض Items/Requests الخاصة بالمستخدم فقط

**المحتوى:**
- `items/` - User's items (Read-Only)
- `requests/` - User's requests (Read-Only) + Offers

---

## 3. Base Layout - التخطيط الأساسي

### 3.1 App Layout (Public)

**File:** `resources/views/layouts/app.blade.php`

**Purpose:** Base layout للصفحات العامة

**Structure:**
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags --}}
    @yield('meta')
    
    {{-- Page Title --}}
    <title>@yield('title', config('app.name', 'Khezana'))</title>
    
    {{-- Canonical URL --}}
    @yield('canonical')
    
    {{-- Styles --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    {{-- Additional Head Content --}}
    @stack('head')
</head>
<body>
    {{-- Header --}}
    @include('partials.header')
    
    {{-- Main Content --}}
    <main id="main-content" role="main">
        @yield('content')
    </main>
    
    {{-- Footer --}}
    @include('partials.footer')
    
    {{-- Scripts (Minimal) --}}
    @stack('scripts')
</body>
</html>
```

**Yield Sections:**
- `@yield('meta')` - Meta tags (robots, og:tags, etc.)
- `@yield('title')` - Page title
- `@yield('canonical')` - Canonical URL
- `@yield('content')` - Main content
- `@stack('head')` - Additional head content
- `@stack('scripts')` - JavaScript (minimal)

**Accessibility:**
- `role="main"` على main element
- Semantic HTML5
- ARIA labels حيث مطلوب

---

### 3.2 User Layout

**File:** `resources/views/layouts/user.blade.php`

**Purpose:** Layout للصفحات الخاصة بالمستخدم (مع Authentication)

**Structure:**
```blade
@extends('layouts.app')

@section('content')
<div class="user-layout">
    {{-- User Navigation (Sidebar or Top) --}}
    @include('partials.user-navigation')
    
    {{-- User Content --}}
    <div class="user-content">
        @yield('user-content')
    </div>
</div>
@endsection
```

**Differences from App Layout:**
- Extends `layouts.app`
- Adds user navigation
- Wraps content in user-specific container

---

## 4. Reusable Components - المكونات القابلة لإعادة الاستخدام

### 4.1 Item Components

#### 4.1.1 ItemCard Component

**File:** `resources/views/components/items/item-card.blade.php`

**Purpose:** عرض Item في قائمة (Browse Items)

**Props:**
```blade
@props([
    'item', // ItemReadModel (required)
])
```

**Structure:**
```blade
<article class="item-card" itemscope itemtype="https://schema.org/Product">
    {{-- Image --}}
    <a href="{{ $item->url }}" class="item-card__image">
        @if($item->primaryImage)
            <img src="{{ $item->primaryImage->path }}" 
                 alt="{{ $item->primaryImage->alt ?? $item->title }}"
                 loading="lazy"
                 itemprop="image">
        @else
            <div class="item-card__placeholder">No Image</div>
        @endif
    </a>
    
    {{-- Content --}}
    <div class="item-card__content">
        {{-- Title --}}
        <h2 class="item-card__title">
            <a href="{{ $item->url }}" itemprop="name">{{ $item->title }}</a>
        </h2>
        
        {{-- Category --}}
        @if($item->category)
            <span class="item-card__category" itemprop="category">
                {{ $item->category->name }}
            </span>
        @endif
        
        {{-- Price --}}
        @if($item->price)
            <div class="item-card__price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                <span itemprop="price">{{ $item->getFormattedPrice() }}</span>
            </div>
        @endif
        
        {{-- Operation Type Badge --}}
        <x-shared.badge 
            :type="$item->operationType" 
            :label="$item->operationTypeLabel" />
        
        {{-- Availability Badge --}}
        <x-shared.badge 
            :type="$item->availabilityStatus" 
            :label="$item->availabilityStatusLabel" />
        
        {{-- Meta --}}
        <div class="item-card__meta">
            <span class="item-card__author" itemprop="seller" itemscope itemtype="https://schema.org/Person">
                <span itemprop="name">{{ $item->user->name ?? 'Unknown' }}</span>
            </span>
            <time datetime="{{ $item->createdAt->toIso8601String() }}" itemprop="datePublished">
                {{ $item->createdAtFormatted }}
            </time>
        </div>
    </div>
</article>
```

**Accessibility:**
- `itemscope itemtype` - Schema.org markup
- `itemprop` attributes - Structured data
- Semantic HTML (`<article>`, `<time>`, etc.)
- Alt text للصور

---

#### 4.1.2 ItemDetails Component

**File:** `resources/views/components/items/item-details.blade.php`

**Purpose:** عرض تفاصيل Item كاملة (View Item Details)

**Props:**
```blade
@props([
    'item', // ItemReadModel (required)
])
```

**Structure:**
```blade
<article class="item-details" itemscope itemtype="https://schema.org/Product">
    {{-- Breadcrumbs --}}
    <x-shared.breadcrumbs :items="[
        ['label' => __('Home'), 'url' => route('home')],
        ['label' => __('Items'), 'url' => route('items.index')],
        ['label' => $item->title, 'url' => $item->url],
    ]" />
    
    {{-- Image Gallery --}}
    <x-shared.image-gallery :images="$item->images" :primary="$item->primaryImage" />
    
    {{-- Main Content --}}
    <div class="item-details__content">
        {{-- Title --}}
        <h1 class="item-details__title" itemprop="name">{{ $item->title }}</h1>
        
        {{-- Meta --}}
        <div class="item-details__meta">
            <x-shared.badge :type="$item->operationType" :label="$item->operationTypeLabel" />
            <x-shared.badge :type="$item->availabilityStatus" :label="$item->availabilityStatusLabel" />
            @if($item->category)
                <span class="item-details__category" itemprop="category">
                    {{ $item->category->name }}
                </span>
            @endif
        </div>
        
        {{-- Price --}}
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
        
        {{-- Description --}}
        <div class="item-details__description" itemprop="description">
            {!! nl2br(e($item->description)) !!}
        </div>
        
        {{-- Attributes --}}
        @if($item->attributes->isNotEmpty())
            <x-shared.attributes-list :attributes="$item->attributes" />
        @endif
        
        {{-- Author --}}
        <div class="item-details__author" itemprop="seller" itemscope itemtype="https://schema.org/Person">
            <span itemprop="name">{{ $item->user->name ?? 'Unknown' }}</span>
            <time datetime="{{ $item->user->createdAt->toIso8601String() ?? '' }}">
                {{ __('Member since') }} {{ $item->user->memberSinceFormatted ?? '' }}
            </time>
        </div>
        
        {{-- Published Date --}}
        <time datetime="{{ $item->createdAt->toIso8601String() }}" itemprop="datePublished" class="item-details__published">
            {{ __('Published') }}: {{ $item->createdAtFormatted }}
        </time>
    </div>
</article>
```

**Accessibility:**
- Schema.org markup كامل
- Semantic HTML
- Structured data

---

### 4.2 Request Components

#### 4.2.1 RequestCard Component

**File:** `resources/views/components/requests/request-card.blade.php`

**Purpose:** عرض Request في قائمة (Browse Requests)

**Props:**
```blade
@props([
    'request', // RequestReadModel (required)
])
```

**Structure:**
```blade
<article class="request-card" itemscope itemtype="https://schema.org/Article">
    {{-- Content --}}
    <div class="request-card__content">
        {{-- Title --}}
        <h2 class="request-card__title">
            <a href="{{ $request->url }}" itemprop="headline">{{ $request->title }}</a>
        </h2>
        
        {{-- Description (Truncated) --}}
        <p class="request-card__description" itemprop="description">
            {{ Str::limit($request->description, 150) }}
        </p>
        
        {{-- Category --}}
        @if($request->category)
            <span class="request-card__category" itemprop="articleSection">
                {{ $request->category->name }}
            </span>
        @endif
        
        {{-- Status Badge --}}
        <x-shared.badge 
            :type="$request->status" 
            :label="$request->statusLabel" />
        
        {{-- Offers Count --}}
        @if($request->offersCount > 0)
            <span class="request-card__offers-count">
                {{ $request->offersCount }} {{ __('offers') }}
            </span>
        @endif
        
        {{-- Meta --}}
        <div class="request-card__meta">
            <span class="request-card__author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                <span itemprop="name">{{ $request->user->name ?? 'Unknown' }}</span>
            </span>
            <time datetime="{{ $request->createdAt->toIso8601String() }}" itemprop="datePublished">
                {{ $request->createdAtFormatted }}
            </time>
        </div>
    </div>
</article>
```

---

#### 4.2.2 RequestDetails Component

**File:** `resources/views/components/requests/request-details.blade.php`

**Purpose:** عرض تفاصيل Request كاملة (View Request Details)

**Props:**
```blade
@props([
    'request', // RequestReadModel (required)
])
```

**Structure:**
```blade
<article class="request-details" itemscope itemtype="https://schema.org/Article">
    {{-- Breadcrumbs --}}
    <x-shared.breadcrumbs :items="[
        ['label' => __('Home'), 'url' => route('home')],
        ['label' => __('Requests'), 'url' => route('requests.index')],
        ['label' => $request->title, 'url' => $request->url],
    ]" />
    
    {{-- Main Content --}}
    <div class="request-details__content">
        {{-- Title --}}
        <h1 class="request-details__title" itemprop="headline">{{ $request->title }}</h1>
        
        {{-- Meta --}}
        <div class="request-details__meta">
            <x-shared.badge :type="$request->status" :label="$request->statusLabel" />
            @if($request->category)
                <span class="request-details__category" itemprop="articleSection">
                    {{ $request->category->name }}
                </span>
            @endif
        </div>
        
        {{-- Description --}}
        <div class="request-details__description" itemprop="articleBody">
            {!! nl2br(e($request->description)) !!}
        </div>
        
        {{-- Attributes --}}
        @if($request->attributes->isNotEmpty())
            <x-shared.attributes-list :attributes="$request->attributes" />
        @endif
        
        {{-- Offers Section (if visible) --}}
        @if($request->offers->isNotEmpty())
            <section class="request-details__offers" aria-label="{{ __('Offers') }}">
                <h2>{{ __('Offers') }} ({{ $request->offers->count() }})</h2>
                @foreach($request->offers as $offer)
                    <x-offers.offer-card :offer="$offer" />
                @endforeach
            </section>
        @elseif($request->offersCount > 0)
            <div class="request-details__offers-count">
                {{ $request->offersCount }} {{ __('pending offers') }}
            </div>
        @endif
        
        {{-- Author --}}
        <div class="request-details__author" itemprop="author" itemscope itemtype="https://schema.org/Person">
            <span itemprop="name">{{ $request->user->name ?? 'Unknown' }}</span>
            <time datetime="{{ $request->user->createdAt->toIso8601String() ?? '' }}">
                {{ __('Member since') }} {{ $request->user->memberSinceFormatted ?? '' }}
            </time>
        </div>
        
        {{-- Published Date --}}
        <time datetime="{{ $request->createdAt->toIso8601String() }}" itemprop="datePublished" class="request-details__published">
            {{ __('Published') }}: {{ $request->createdAtFormatted }}
        </time>
    </div>
</article>
```

---

### 4.3 Offer Components

#### 4.3.1 OfferCard Component

**File:** `resources/views/components/offers/offer-card.blade.php`

**Purpose:** عرض Offer في قائمة (View Offers for Request)

**Props:**
```blade
@props([
    'offer', // OfferReadModel (required)
])
```

**Structure:**
```blade
<article class="offer-card">
    {{-- Offer Author --}}
    <div class="offer-card__author">
        <span>{{ $offer->user->name ?? 'Unknown' }}</span>
        <time datetime="{{ $offer->createdAt->toIso8601String() }}">
            {{ $offer->createdAtFormatted }}
        </time>
    </div>
    
    {{-- Offer Content --}}
    <div class="offer-card__content">
        {{-- Linked Item (if exists) --}}
        @if($offer->item)
            <div class="offer-card__item">
                <a href="{{ $offer->item->url }}">
                    {{ $offer->item->title }}
                </a>
                @if($offer->item->primaryImage)
                    <img src="{{ $offer->item->primaryImage->path }}" 
                         alt="{{ $offer->item->title }}"
                         loading="lazy">
                @endif
            </div>
        @endif
        
        {{-- Operation Type --}}
        <x-shared.badge 
            :type="$offer->operationType" 
            :label="$offer->operationTypeLabel" />
        
        {{-- Price --}}
        @if($offer->price)
            <div class="offer-card__price">
                {{ number_format($offer->price, 2) }} {{ config('app.currency', 'SAR') }}
            </div>
        @endif
        
        {{-- Deposit --}}
        @if($offer->depositAmount)
            <div class="offer-card__deposit">
                {{ __('Deposit') }}: {{ number_format($offer->depositAmount, 2) }} {{ config('app.currency', 'SAR') }}
            </div>
        @endif
        
        {{-- Message --}}
        @if($offer->message)
            <p class="offer-card__message">{{ $offer->message }}</p>
        @endif
        
        {{-- Status Badge --}}
        <x-shared.badge 
            :type="$offer->status" 
            :label="$offer->statusLabel" />
    </div>
</article>
```

---

### 4.4 Shared Components

#### 4.4.1 EmptyState Component

**File:** `resources/views/components/shared/empty-state.blade.php`

**Purpose:** عرض حالة فارغة (No results found)

**Props:**
```blade
@props([
    'title' => __('No results found'), // string (required)
    'message' => null, // string (optional)
    'icon' => null, // string (optional, icon name)
    'action' => null, // array (optional, ['label' => string, 'url' => string])
])
```

**Structure:**
```blade
<div class="empty-state" role="status" aria-live="polite">
    @if($icon)
        <div class="empty-state__icon" aria-hidden="true">
            {{-- Icon SVG or emoji --}}
        </div>
    @endif
    
    <h2 class="empty-state__title">{{ $title }}</h2>
    
    @if($message)
        <p class="empty-state__message">{{ $message }}</p>
    @endif
    
    @if($action)
        <a href="{{ $action['url'] }}" class="empty-state__action">
            {{ $action['label'] }}
        </a>
    @endif
</div>
```

**Usage Examples:**
```blade
{{-- Browse Items Empty --}}
<x-shared.empty-state 
    :title="__('No items found')"
    :message="__('Try adjusting your filters or check back later.')"
    :action="['label' => __('Browse All Items'), 'url' => route('items.index')]" />

{{-- Search Empty --}}
<x-shared.empty-state 
    :title="__('No results found for :query', ['query' => $query])"
    :message="__('Try different keywords or browse all items.')" />

{{-- User Items Empty --}}
<x-shared.empty-state 
    :title="__('You haven't created any items yet')"
    :message="__('Start by creating your first item.')"
    :action="['label' => __('Create Item'), 'url' => route('items.create')]" />
```

**Accessibility:**
- `role="status"` - Screen reader announcement
- `aria-live="polite"` - Dynamic content updates

---

#### 4.4.2 Pagination Component

**File:** `resources/views/components/shared/pagination.blade.php`

**Purpose:** عرض Pagination links

**Props:**
```blade
@props([
    'paginator', // LengthAwarePaginator (required)
])
```

**Structure:**
```blade
@if($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        {{-- Previous Link --}}
        @if($paginator->onFirstPage())
            <span class="pagination__link pagination__link--disabled" aria-disabled="true">
                {{ __('Previous') }}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" 
               class="pagination__link pagination__link--prev"
               rel="prev"
               aria-label="{{ __('Go to previous page') }}">
                {{ __('Previous') }}
            </a>
        @endif
        
        {{-- Page Numbers --}}
        <div class="pagination__pages">
            @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if($page == $paginator->currentPage())
                    <span class="pagination__page pagination__page--current" 
                          aria-current="page"
                          aria-label="{{ __('Page :page', ['page' => $page]) }}">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" 
                       class="pagination__page"
                       aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        </div>
        
        {{-- Next Link --}}
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" 
               class="pagination__link pagination__link--next"
               rel="next"
               aria-label="{{ __('Go to next page') }}">
                {{ __('Next') }}
            </a>
        @else
            <span class="pagination__link pagination__link--disabled" aria-disabled="true">
                {{ __('Next') }}
            </span>
        @endif
        
        {{-- Info --}}
        <div class="pagination__info" aria-live="polite">
            {{ __('Showing :from to :to of :total results', [
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ]) }}
        </div>
    </nav>
@endif
```

**Accessibility:**
- `role="navigation"` - Navigation landmark
- `aria-label` - Navigation purpose
- `aria-current="page"` - Current page indicator
- `rel="prev"` / `rel="next"` - Link relationships

---

#### 4.4.3 Badge Component

**File:** `resources/views/components/shared/badge.blade.php`

**Purpose:** عرض Badge (Status, Availability, Operation Type)

**Props:**
```blade
@props([
    'type', // string (required, e.g., 'available', 'pending', 'sell')
    'label', // string (required, display label)
    'variant' => 'default', // string (optional, 'default' | 'success' | 'warning' | 'danger' | 'info')
])
```

**Structure:**
```blade
<span class="badge badge--{{ $variant }}" 
      data-type="{{ $type }}"
      aria-label="{{ $label }}">
    {{ $label }}
</span>
```

**Variant Mapping:**
```php
// Automatic variant based on type
$variant = match($type) {
    'available', 'approved', 'open', 'accepted' => 'success',
    'unavailable', 'rejected', 'closed', 'cancelled' => 'danger',
    'pending' => 'warning',
    'fulfilled', 'archived' => 'info',
    default => 'default',
};
```

**Usage:**
```blade
<x-shared.badge :type="$item->operationType" :label="$item->operationTypeLabel" />
<x-shared.badge :type="$item->availabilityStatus" :label="$item->availabilityStatusLabel" />
<x-shared.badge :type="$request->status" :label="$request->statusLabel" />
```

---

#### 4.4.4 ImageGallery Component

**File:** `resources/views/components/shared/image-gallery.blade.php`

**Purpose:** عرض معرض صور (Item Images)

**Props:**
```blade
@props([
    'images', // Collection<ImageReadModel> (required)
    'primary', // ImageReadModel|null (optional, primary image)
])
```

**Structure:**
```blade
<div class="image-gallery" role="group" aria-label="{{ __('Item Images') }}">
    {{-- Primary Image (Large) --}}
    @if($primary)
        <div class="image-gallery__primary">
            <img src="{{ $primary->path }}" 
                 alt="{{ $primary->alt ?? __('Primary image') }}"
                 loading="eager"
                 itemprop="image">
        </div>
    @endif
    
    {{-- Thumbnail Gallery --}}
    @if($images->count() > 1)
        <div class="image-gallery__thumbnails" role="list">
            @foreach($images as $image)
                <div class="image-gallery__thumbnail" role="listitem">
                    <img src="{{ $image->path }}" 
                         alt="{{ $image->alt ?? __('Image :number', ['number' => $loop->iteration]) }}"
                         loading="lazy"
                         data-full-size="{{ $image->path }}">
                </div>
            @endforeach
        </div>
    @endif
</div>
```

**Performance:**
- Primary image: `loading="eager"` (above the fold)
- Thumbnails: `loading="lazy"` (below the fold)
- No JavaScript required (progressive enhancement for lightbox)

---

#### 4.4.5 AttributesList Component

**File:** `resources/views/components/shared/attributes-list.blade.php`

**Purpose:** عرض قائمة Attributes الديناميكية

**Props:**
```blade
@props([
    'attributes', // Collection<AttributeReadModel> (required)
])
```

**Structure:**
```blade
<dl class="attributes-list" role="list">
    @foreach($attributes as $attribute)
        <div class="attributes-list__item" role="listitem">
            <dt class="attributes-list__name">{{ $attribute->name }}:</dt>
            <dd class="attributes-list__value">{{ $attribute->formattedValue }}</dd>
        </div>
    @endforeach
</dl>
```

**Accessibility:**
- `<dl>` (Definition List) - Semantic HTML
- `role="list"` - Screen reader support

---

#### 4.4.6 Breadcrumbs Component

**File:** `resources/views/components/shared/breadcrumbs.blade.php`

**Purpose:** عرض Breadcrumbs navigation

**Props:**
```blade
@props([
    'items', // array (required, [['label' => string, 'url' => string], ...])
])
```

**Structure:**
```blade
<nav class="breadcrumbs" aria-label="{{ __('Breadcrumb') }}">
    <ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
        @foreach($items as $index => $item)
            <li class="breadcrumbs__item" 
                itemprop="itemListElement" 
                itemscope 
                itemtype="https://schema.org/ListItem">
                @if($loop->last)
                    <span itemprop="name">{{ $item['label'] }}</span>
                @else
                    <a href="{{ $item['url'] }}" itemprop="item">
                        <span itemprop="name">{{ $item['label'] }}</span>
                    </a>
                @endif
                <meta itemprop="position" content="{{ $index + 1 }}" />
            </li>
        @endforeach
    </ol>
</nav>
```

**Accessibility:**
- `<nav>` - Navigation landmark
- `aria-label` - Navigation purpose
- Schema.org BreadcrumbList markup

---

### 4.5 SEO Components

#### 4.5.1 MetaTags Component

**File:** `resources/views/components/seo/meta-tags.blade.php`

**Purpose:** عرض Meta tags من Read Model

**Props:**
```blade
@props([
    'metaTags', // array (required, from ReadModel->metaTags)
])
```

**Structure:**
```blade
{{-- Robots --}}
@if(isset($metaTags['robots']))
    <meta name="robots" content="{{ $metaTags['robots'] }}">
@endif

{{-- Open Graph --}}
@if(isset($metaTags['og:type']))
    <meta property="og:type" content="{{ $metaTags['og:type'] }}">
@endif

@if(isset($metaTags['og:title']))
    <meta property="og:title" content="{{ $metaTags['og:title'] }}">
@endif

@if(isset($metaTags['og:description']))
    <meta property="og:description" content="{{ $metaTags['og:description'] }}">
@endif

@if(isset($metaTags['og:image']))
    <meta property="og:image" content="{{ $metaTags['og:image'] }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
@if(isset($metaTags['og:title']))
    <meta name="twitter:title" content="{{ $metaTags['og:title'] }}">
@endif
@if(isset($metaTags['og:description']))
    <meta name="twitter:description" content="{{ $metaTags['og:description'] }}">
@endif
@if(isset($metaTags['og:image']))
    <meta name="twitter:image" content="{{ $metaTags['og:image'] }}">
@endif
```

---

#### 4.5.2 Canonical Component

**File:** `resources/views/components/seo/canonical.blade.php`

**Purpose:** عرض Canonical URL

**Props:**
```blade
@props([
    'url', // string (required, canonical URL)
])
```

**Structure:**
```blade
<link rel="canonical" href="{{ $url }}">
```

---

## 5. Empty State Design - تصميم الحالات الفارغة

### 5.1 متى يظهر Empty State؟

**Empty State يظهر في الحالات التالية:**

1. ✅ Browse/Search: `$items->isEmpty()` أو `$items->total() === 0`
2. ✅ User Items: `$items->isEmpty()` (User hasn't created items)
3. ✅ User Requests: `$requests->isEmpty()` (User hasn't created requests)
4. ✅ Offers: `$offers->isEmpty()` (No offers found)

**⚠️ قاعدة:** Empty State **لا يظهر** في:
- 404 pages (Controller handles with `abort(404)`)
- 403 pages (Controller handles with `abort(403)`)

---

### 5.2 Empty State Messages

#### 5.2.1 Browse Items Empty

**Title:** `__('No items found')`

**Message:** `__('Try adjusting your filters or check back later.')`

**CTA (Optional):**
```php
['label' => __('Browse All Items'), 'url' => route('items.index')]
```

---

#### 5.2.2 Search Empty

**Title:** `__('No results found for :query', ['query' => $query])`

**Message:** `__('Try different keywords or browse all items.')`

**CTA (Optional):**
```php
['label' => __('Browse All Items'), 'url' => route('items.index')]
```

---

#### 5.2.3 User Items Empty

**Title:** `__('You haven't created any items yet')`

**Message:** `__('Start by creating your first item.')`

**CTA (Optional):**
```php
['label' => __('Create Item'), 'url' => route('items.create')]
```

**⚠️ ملاحظة:** CTA قد يشير إلى Write Layer (Create Action) - هذا خارج نطاق Read Flow الحالي.

---

#### 5.2.4 Offers Empty

**Title:** `__('No offers found')`

**Message:** `__('No one has made an offer on this request yet.')`

**CTA:** None (Read-Only context)

---

### 5.3 Empty State Design Rules

**Visual Design:**
- Icon (optional) - Visual indicator
- Title - Clear, concise message
- Message (optional) - Helpful guidance
- CTA (optional) - Action button if applicable

**Accessibility:**
- `role="status"` - Screen reader announcement
- `aria-live="polite"` - Dynamic content updates
- Semantic HTML

---

## 6. SEO Integration - تكامل SEO

### 6.1 Meta Tags Integration

#### 6.1.1 Item Details Page

**View:** `resources/views/public/items/show.blade.php`

**Structure:**
```blade
@extends('layouts.app')

@section('meta')
    <x-seo.meta-tags :metaTags="$item->metaTags" />
@endsection

@section('canonical')
    <x-seo.canonical :url="$item->canonicalUrl" />
@endsection

@section('title', $item->title . ' - ' . config('app.name'))

@section('content')
    <x-items.item-details :item="$item" />
@endsection
```

---

#### 6.1.2 Request Details Page

**View:** `resources/views/public/requests/show.blade.php`

**Structure:**
```blade
@extends('layouts.app')

@section('meta')
    <x-seo.meta-tags :metaTags="$request->metaTags" />
@endsection

@section('canonical')
    <x-seo.canonical :url="$request->canonicalUrl" />
@endsection

@section('title', $request->title . ' - ' . config('app.name'))

@section('content')
    <x-requests.request-details :request="$request" />
@endsection
```

---

#### 6.1.3 Search Results Pages

**View:** `resources/views/public/search/items.blade.php`

**Structure:**
```blade
@extends('layouts.app')

@section('meta')
    <meta name="robots" content="noindex, follow">
@endsection

@section('title', __('Search Results for :query', ['query' => $query]) . ' - ' . config('app.name'))

@section('content')
    {{-- Search results with noindex --}}
@endsection
```

**⚠️ قاعدة:** Search Results **يجب** أن تكون `noindex` (PUBLIC_READ_FLOW.md Section 5.4.2).

---

#### 6.1.4 Offers Pages

**View:** `resources/views/user/requests/offers.blade.php`

**Structure:**
```blade
@extends('layouts.user')

@section('meta')
    <meta name="robots" content="noindex, follow">
@endsection

@section('title', __('Offers') . ' - ' . config('app.name'))

@section('user-content')
    {{-- Offers list with noindex --}}
@endsection
```

**⚠️ قاعدة:** Offers Pages **يجب** أن تكون `noindex` (PUBLIC_READ_FLOW.md Section 5.4.2).

---

### 6.2 Canonical URLs

**Canonical URL Rules (من PUBLIC_READ_FLOW.md Section 5.3.1):**

| الصفحة | Canonical URL | ملاحظات |
|--------|--------------|---------|
| Item Details | `/items/{id}/{slug}` | دائماً |
| Request Details | `/requests/{id}/{slug}` | دائماً |
| Browse Items | `/items` | بدون query parameters |
| Browse Requests | `/requests` | بدون query parameters |
| Search | `{base_url}/items/search?q={query}` | مع query parameter |

**Implementation:**
- Canonical URL يأتي من Read Model (`$item->canonicalUrl`)
- View يستخدم `<x-seo.canonical>` component

---

### 6.3 Structured Data (Schema.org)

#### 6.3.1 Item Schema

**Type:** `https://schema.org/Product`

**Required Properties:**
- `name` - Item title
- `description` - Item description
- `image` - Primary image
- `offers` - Price information
- `seller` - User information

**Implementation:**
- Schema markup في `ItemCard` و `ItemDetails` components
- `itemscope itemtype` attributes
- `itemprop` attributes

---

#### 6.3.2 Request Schema

**Type:** `https://schema.org/Article`

**Required Properties:**
- `headline` - Request title
- `articleBody` - Request description
- `author` - User information
- `datePublished` - Created date

**Implementation:**
- Schema markup في `RequestCard` و `RequestDetails` components

---

## 7. Performance Rules - قواعد الأداء

### 7.1 No JavaScript Required

**⚠️ قاعدة:** الصفحات **يجب** أن تعمل بدون JavaScript.

**Allowed JavaScript (Progressive Enhancement):**
- Image lightbox (optional enhancement)
- Form validation (optional enhancement)
- Smooth scrolling (optional enhancement)

**Forbidden JavaScript:**
- Required for page functionality
- Required for navigation
- Required for content display

---

### 7.2 Image Optimization

#### 7.2.1 Lazy Loading

**Primary Image:**
- `loading="eager"` - Above the fold
- No lazy loading

**Thumbnail Images:**
- `loading="lazy"` - Below the fold
- Lazy loading enabled

**Gallery Images:**
- `loading="lazy"` - All except primary

---

#### 7.2.2 Image Attributes

**Required Attributes:**
- `src` - Image URL
- `alt` - Alt text (from Read Model)
- `loading` - Lazy loading attribute

**Optional Attributes:**
- `width` / `height` - For layout stability (if known)
- `srcset` - Responsive images (if multiple sizes available)

---

### 7.3 CSS Optimization

**Rules:**
- Single CSS file (`app.css`)
- Minimal CSS (no external frameworks)
- Critical CSS inline (optional, for above-the-fold)
- No unused CSS

**File Structure:**
```
public/
└── css/
    └── app.css  # Single CSS file
```

---

### 7.4 HTML Optimization

**Rules:**
- Semantic HTML5
- Minimal nesting
- No inline styles (except critical CSS)
- No inline scripts (except progressive enhancement)

---

### 7.5 Asset Loading

**Critical Assets:**
- CSS (blocking)
- Fonts (system fonts preferred)

**Non-Critical Assets:**
- JavaScript (deferred/async)
- Images (lazy loaded)

---

## 8. Accessibility Rules - قواعد إمكانية الوصول

### 8.1 Semantic HTML

**Required Elements:**
- `<header>` - Site header
- `<nav>` - Navigation
- `<main>` - Main content
- `<article>` - Item/Request content
- `<section>` - Content sections
- `<footer>` - Site footer
- `<time>` - Dates/timestamps
- `<dl>` / `<dt>` / `<dd>` - Definition lists (attributes)

---

### 8.2 ARIA Attributes

**Required ARIA:**
- `role="main"` - Main content area
- `role="navigation"` - Navigation areas
- `role="status"` - Empty states
- `aria-label` - Button/link labels
- `aria-current="page"` - Current page indicator
- `aria-live="polite"` - Dynamic content updates
- `aria-disabled="true"` - Disabled elements

---

### 8.3 Keyboard Navigation

**Required:**
- All interactive elements keyboard accessible
- Focus indicators visible
- Tab order logical
- Skip links for main content

---

### 8.4 Screen Reader Support

**Required:**
- Alt text for all images
- Descriptive link text
- Form labels
- Error messages
- Status announcements

---

## 9. View Implementation Examples - أمثلة التنفيذ

### 9.1 Browse Items Page

**File:** `resources/views/public/items/index.blade.php`

**Structure:**
```blade
@extends('layouts.app')

@section('title', __('Items') . ' - ' . config('app.name'))

@section('canonical')
    <x-seo.canonical :url="route('items.index')" />
@endsection

@section('meta')
    <meta name="robots" content="index, follow">
@endsection

@section('content')
    <div class="items-page">
        {{-- Page Header --}}
        <header class="items-page__header">
            <h1>{{ __('Browse Items') }}</h1>
        </header>
        
        {{-- Filters (Optional) --}}
        @if(isset($filters) && !empty(array_filter($filters)))
            <div class="items-page__filters" role="region" aria-label="{{ __('Filters') }}">
                {{-- Filter UI --}}
            </div>
        @endif
        
        {{-- Items List --}}
        @if($items->isEmpty())
            <x-shared.empty-state 
                :title="__('No items found')"
                :message="__('Try adjusting your filters or check back later.')" />
        @else
            <div class="items-page__list" role="list">
                @foreach($items as $item)
                    <x-items.item-card :item="$item" />
                @endforeach
            </div>
            
            {{-- Pagination --}}
            <x-shared.pagination :paginator="$items" />
        @endif
    </div>
@endsection
```

---

### 9.2 View Item Details Page

**File:** `resources/views/public/items/show.blade.php`

**Structure:**
```blade
@extends('layouts.app')

@section('meta')
    <x-seo.meta-tags :metaTags="$item->metaTags" />
@endsection

@section('canonical')
    <x-seo.canonical :url="$item->canonicalUrl" />
@endsection

@section('title', $item->title . ' - ' . config('app.name'))

@section('content')
    <div class="item-page">
        <x-items.item-details :item="$item" />
    </div>
@endsection
```

---

### 9.3 Search Results Page

**File:** `resources/views/public/search/items.blade.php`

**Structure:**
```blade
@extends('layouts.app')

@section('meta')
    <meta name="robots" content="noindex, follow">
@endsection

@section('title', __('Search Results for :query', ['query' => $query]) . ' - ' . config('app.name'))

@section('content')
    <div class="search-page">
        <header class="search-page__header">
            <h1>{{ __('Search Results') }}</h1>
            <p>{{ __('Found :count results for :query', ['count' => $items->total(), 'query' => $query]) }}</p>
        </header>
        
        @if($items->isEmpty())
            <x-shared.empty-state 
                :title="__('No results found for :query', ['query' => $query])"
                :message="__('Try different keywords or browse all items.')"
                :action="['label' => __('Browse All Items'), 'url' => route('items.index')]" />
        @else
            <div class="search-page__results" role="list">
                @foreach($items as $item)
                    <x-items.item-card :item="$item" />
                @endforeach
            </div>
            
            <x-shared.pagination :paginator="$items" />
        @endif
    </div>
@endsection
```

---

## 10. Summary - الملخص

### 10.1 القواعد الإلزامية

1. ✅ **Blade Only**: لا Vue، لا React
2. ✅ **SSR First**: Server-Side Rendering أولوية
3. ✅ **Components-Based**: تصميم قائم على Components
4. ✅ **SEO-Friendly**: Meta tags، Canonical URLs، Schema.org
5. ✅ **Accessibility**: ARIA، Semantic HTML، Keyboard navigation
6. ✅ **Performance**: Lazy loading، Minimal assets، No JS required

### 10.2 المرجع الإلزامي

**هذا التصميم مبني بالكامل على:**
- `CONTROLLERS_LAYER_DESIGN.md` - View Contracts و Variables
- `PUBLIC_READ_FLOW.md` - SEO Rules و URL Structure

**⚠️ أي تنفيذ يجب أن يلتزم بهذا التصميم 100%.**

---

**الإصدار:** 1.0  
**آخر تحديث:** 2026-01-20  
**الحالة:** ✅ Approved for Implementation
