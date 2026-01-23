# Khezana - Complete Blade Views & Interfaces Documentation

## 📋 Table of Contents

1. [Overview](#overview)
2. [Architecture Principles](#architecture-principles)
3. [File Structure](#file-structure)
4. [Layout System](#layout-system)
5. [Views Organization](#views-organization)
6. [ViewModel Pattern](#viewmodel-pattern)
7. [Partials & Components](#partials--components)
8. [CSS Architecture](#css-architecture)
9. [How It All Works Together](#how-it-all-works-together)
10. [Examples & Usage](#examples--usage)
11. [Best Practices](#best-practices)

---

## Overview

The Khezana project uses **Laravel Blade** templating engine to build a modern, RTL (Right-to-Left) Arabic-first interface. The architecture follows a **feature-based, component-driven approach** with:

- **Feature-Based Organization** - Views organized by feature (items, requests, profile, pages)
- **ViewModel Pattern** - Separation of presentation logic from views
- **Component-Based Partials** - Reusable UI components (item-card, pagination, empty-state)
- **Modular CSS** - Component-based and page-based CSS architecture
- **Layout System** - Base layouts for different page types
- **RTL Support** - Full Arabic language support with logical CSS properties
- **SEO-Friendly** - Meta tags, semantic HTML, structured data

### Key Technologies

- **Laravel 12.47.0** - PHP Framework
- **Blade Templates** - Templating engine
- **Custom CSS** - Modular design system with BEM naming
- **Vite** - Asset bundling
- **ViewModel Pattern** - Presentation logic separation

---

## Architecture Principles

### 1. Feature-Based Organization
Views are organized by **feature** rather than just by type (public/auth):
- `items/` - Items management feature
- `requests/` - Requests management feature
- `profile/` - User profile feature
- `pages/` - Static pages feature
- `public/items/` - Public items feature
- `public/requests/` - Public requests feature

### 2. Single Responsibility Principle
Each partial/component has a single, well-defined responsibility:
- `page-header.blade.php` - Only page header
- `grid.blade.php` - Only grid display
- `empty-state.blade.php` - Only empty state
- `pagination.blade.php` - Only pagination

### 3. ViewModel Pattern
All presentation logic is extracted from Blade views into ViewModels:
- `ItemCardViewModel` - Item card presentation logic
- `ItemDetailViewModel` - Item detail presentation logic
- `RequestCardViewModel` - Request card presentation logic
- `ProfileViewModel` - Profile presentation logic

### 4. Dumb Components
UI components receive data via props and contain minimal to no internal logic:
- `item-card.blade.php` - Pure presentation component
- All partials are simple and reusable

### 5. RTL-First Design
- Logical CSS properties (`margin-inline-start`, `padding-inline-end`)
- `[dir="rtl"]` selectors for RTL-specific styles
- Full Arabic language support

---

## File Structure

```
resources/views/
├── auth/                              # Authentication pages
│   ├── login.blade.php
│   └── register.blade.php
│
├── filament/                          # Admin panel custom pages
│   └── pages/
│       └── platform-settings.blade.php
│
├── home/                              # Homepage
│   └── index.blade.php
│
├── items/                             # Feature: Items Management (Authenticated)
│   ├── create.blade.php              # Create new item form
│   ├── edit.blade.php                # Edit item form
│   ├── index.blade.php               # User's items listing (lightweight)
│   ├── show.blade.php                # Item detail page
│   └── _partials/                     # Feature-specific partials
│       ├── page-header.blade.php     # Page header
│       ├── grid.blade.php            # Items grid
│       ├── empty-state.blade.php     # Empty state
│       ├── pagination.blade.php      # Pagination
│       └── detail/                   # Detail page partials
│           ├── breadcrumb.blade.php
│           ├── header.blade.php
│           ├── images.blade.php
│           ├── price.blade.php
│           ├── meta.blade.php
│           ├── attributes.blade.php
│           ├── description.blade.php
│           ├── approval-info.blade.php
│           ├── actions.blade.php
│           ├── additional-info.blade.php
│           ├── delete-modal.blade.php
│           └── scripts.blade.php
│
├── requests/                          # Feature: Requests Management (Authenticated)
│   ├── create.blade.php              # Create request form
│   ├── index.blade.php               # User's requests listing (lightweight)
│   ├── show.blade.php                # Request detail page
│   └── _partials/                     # Feature-specific partials
│       ├── page-header.blade.php
│       ├── grid.blade.php
│       ├── empty-state.blade.php
│       └── pagination.blade.php
│
├── profile/                           # Feature: User Profile
│   ├── show.blade.php                # Profile overview
│   ├── edit.blade.php                # Edit profile
│   ├── password.blade.php            # Security settings
│   └── _partials/
│       └── navigation.blade.php     # Profile navigation sidebar
│
├── pages/                             # Feature: Static Pages
│   ├── _layout.blade.php             # Shared layout for static pages
│   ├── terms.blade.php               # Terms and Conditions
│   ├── privacy.blade.php             # Privacy Policy
│   ├── how-it-works.blade.php        # How It Works
│   └── fees.blade.php                # Fees and Commissions
│
├── public/                            # Public-facing features
│   ├── items/
│   │   ├── index.blade.php          # Public items listing (lightweight)
│   │   ├── show.blade.php           # Public item detail
│   │   └── _partials/
│   │       ├── page-header.blade.php
│   │       ├── grid.blade.php
│   │       ├── empty-state.blade.php
│   │       ├── pagination.blade.php
│   │       └── detail/              # Detail page partials
│   │           ├── breadcrumb.blade.php
│   │           ├── header.blade.php
│   │           ├── images.blade.php
│   │           ├── price.blade.php
│   │           ├── meta.blade.php
│   │           ├── attributes.blade.php
│   │           ├── description.blade.php
│   │           ├── contact-form.blade.php
│   │           ├── cta.blade.php
│   │           ├── additional-info.blade.php
│   │           ├── image-modal.blade.php
│   │           └── scripts.blade.php
│   └── requests/
│       ├── create-info.blade.php    # Request info page
│       ├── index.blade.php          # Public requests listing (lightweight)
│       ├── show.blade.php           # Public request detail
│       └── _partials/
│           ├── page-header.blade.php
│           ├── grid.blade.php
│           ├── empty-state.blade.php
│           └── pagination.blade.php
│
├── partials/                          # Shared components (global)
│   ├── header.blade.php              # Site header/navigation
│   ├── footer.blade.php              # Site footer
│   └── item-card/
│       ├── item-card.blade.php       # Item card component (dumb component)
│       ├── README.md                 # Component documentation
│       └── USAGE.md                  # Usage examples
│
└── layouts/                           # Base layouts
    ├── app.blade.php                 # Main application layout
    ├── auth.blade.php                # Authentication layout
    └── home.blade.php                # Homepage layout

app/ViewModels/                        # ViewModel Pattern
├── Items/
│   ├── ItemCardViewModel.php        # Item card presentation logic
│   └── ItemDetailViewModel.php      # Item detail presentation logic
├── Requests/
│   └── RequestCardViewModel.php     # Request card presentation logic
├── Profile/
│   └── ProfileViewModel.php         # Profile presentation logic
└── README.md                         # ViewModel documentation

public/css/
├── home.css                          # Main CSS entry point (imports all)
├── variables.css                     # Design tokens & CSS variables
├── base.css                          # Reset & base styles
├── layout.css                       # Layout & grid systems
├── header.css                        # Header & navigation
├── buttons.css                       # Button components
├── hero.css                          # Hero section
├── sections.css                      # Page sections
├── forms.css                         # Form elements
├── footer.css                        # Footer
├── auth.css                          # Authentication pages
├── responsive-improvements.css       # Responsive utilities
├── components/                       # Component-based CSS
│   ├── item-card.css                # Item card styles
│   ├── pagination.css               # Pagination styles
│   └── empty-state.css              # Empty state styles
└── pages/                            # Page-based CSS
    ├── items-index.css              # Items listing page
    ├── items-show.css               # Item detail page
    ├── requests-index.css           # Requests listing page
    ├── static-pages.css             # Static pages
    └── profile.css                   # Profile pages
```

---

## Layout System

### 1. Main Application Layout (`layouts/app.blade.php`)

**Purpose**: Base layout for all authenticated and public pages

**Structure**:
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Khezana'))</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-improvements.css') }}">
    
    @stack('meta')      <!-- SEO meta tags -->
    @stack('styles')    <!-- Additional styles -->
</head>
<body class="font-sans antialiased">
    @include('partials.header')
    
    <main>
        @yield('content')
    </main>
    
    @include('partials.footer')
    
    @stack('scripts')
</body>
</html>
```

**Features**:
- RTL (Right-to-Left) support
- CSRF token included
- Responsive viewport
- Font loading (Figtree)
- Stack system for additional styles/scripts
- SEO meta tags support via `@stack('meta')`

**Usage**:
```blade
@extends('layouts.app')

@section('title', 'Page Title - ' . config('app.name'))

@push('meta')
    <meta name="description" content="Page description">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
    <!-- Your page content -->
@endsection
```

### 2. Homepage Layout (`layouts/home.blade.php`)

**Purpose**: Special layout for homepage with lazy loading support

**Differences from app layout**:
- Includes lazy loading script for images
- Fixed title structure
- Optimized for homepage performance

**Usage**:
```blade
@extends('layouts.home')

@section('content')
    <!-- Homepage content -->
@endsection
```

### 3. Authentication Layout (`layouts/auth.blade.php`)

**Purpose**: Minimal layout for login/register pages

**Features**:
- Simple logo header
- Centered content area
- No footer
- Clean, focused design

**Usage**:
```blade
@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <!-- Auth form -->
@endsection
```

### 4. Static Pages Layout (`pages/_layout.blade.php`)

**Purpose**: Shared layout for static pages (Terms, Privacy, etc.)

**Features**:
- Breadcrumb navigation
- Page header with title
- SEO meta tags support
- Consistent styling

**Usage**:
```blade
@extends('pages._layout')

@section('title', __('pages.terms.title'))

@push('meta')
    <meta name="description" content="{{ __('pages.terms.meta_description') }}">
    <link rel="canonical" href="{{ route('pages.terms') }}">
@endpush

@section('page-content')
    <!-- Page content -->
@endsection
```

---

## Views Organization

### Feature-Based Structure

Each feature has its own directory with:
- Main views (index, show, create, edit)
- `_partials/` directory for feature-specific partials
- Lightweight main views that include partials

### Public Views (No Authentication Required)

#### 1. Homepage (`home/index.blade.php`)

**Route**: `/`  
**Controller**: `HomeController@index`  
**Layout**: `layouts.home`

**Sections**:
- **Hero Section**: Main call-to-action
- **Services Section**: 4 service cards (Sell, Rent, Donate, Request)
- **How It Works**: 3-step process explanation
- **Trust Indicators**: Security & reliability badges
- **Featured Items**: Sections for Sell, Rent, Donate
- **Call to Action**: Final CTA section

**Key Features**:
- Dynamic featured items loading
- Responsive grid layouts
- Lazy image loading

#### 2. Public Items Listing (`public/items/index.blade.php`)

**Route**: `/items`  
**Controller**: `Public\ItemController@index`  
**Layout**: `layouts.app`

**Structure** (Lightweight View):
```blade
@extends('layouts.app')

@section('title', __('items.title') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            @include('public.items._partials.page-header', ['items' => $items])
            
            <main class="khezana-listing-main">
                @if ($items->count() > 0)
                    @include('public.items._partials.grid', ['items' => $items])
                    @include('public.items._partials.pagination', ['items' => $items])
                @else
                    @include('public.items._partials.empty-state')
                @endif
            </main>
        </div>
    </div>
@endsection
```

**Data Passed**:
- `$items` - Paginated items collection (ItemReadModel)
- `$sort` - Current sort option

**Partials Used**:
- `page-header.blade.php` - Page title, count, actions
- `grid.blade.php` - Items grid using item-card component
- `pagination.blade.php` - Pagination links
- `empty-state.blade.php` - Empty state message

#### 3. Public Item Detail (`public/items/show.blade.php`)

**Route**: `/items/{id}/{slug?}`  
**Controller**: `Public\ItemController@show`  
**Layout**: `layouts.app`

**Structure** (Uses ViewModel):
```blade
@extends('layouts.app')

@section('title', $viewModel->title . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-item-detail-page">
        <div class="khezana-container">
            @include('public.items._partials.detail.breadcrumb', ['viewModel' => $viewModel])
            @include('public.items._partials.detail.header', ['viewModel' => $viewModel])
            @include('public.items._partials.detail.images', ['viewModel' => $viewModel])
            @include('public.items._partials.detail.price', ['viewModel' => $viewModel])
            @include('public.items._partials.detail.meta', ['viewModel' => $viewModel])
            @include('public.items._partials.detail.attributes', ['viewModel' => $viewModel])
            @include('public.items._partials.detail.description', ['viewModel' => $viewModel])
            @include('public.items._partials.detail.contact-form', ['viewModel' => $viewModel])
            @include('public.items._partials.detail.cta', ['viewModel' => $viewModel])
            @include('public.items._partials.detail.additional-info', ['viewModel' => $viewModel])
        </div>
    </div>
    
    @include('public.items._partials.detail.image-modal', ['viewModel' => $viewModel])
    @include('public.items._partials.detail.scripts', ['viewModel' => $viewModel])
@endsection
```

**Features**:
- Uses `ItemDetailViewModel` for all presentation logic
- Image gallery with zoom
- Contact form (authenticated users)
- Breadcrumb navigation
- Keyboard navigation (arrows, ESC)
- Touch gestures for mobile

**ViewModel**: `ItemDetailViewModel::fromItem($item, 'public')`

#### 4. Static Pages (`pages/`)

**Routes**:
- `/terms` - Terms and Conditions
- `/privacy` - Privacy Policy
- `/how-it-works` - How It Works
- `/fees` - Fees and Commissions

**Controller**: `PageController`

**Features**:
- SEO-friendly (meta tags, canonical URLs)
- Full translation support
- RTL support
- Clean, readable design

### Authenticated Views (User's Own Content)

#### 1. User Items Listing (`items/index.blade.php`)

**Route**: `/my-items`  
**Controller**: `ItemController@index`  
**Layout**: `layouts.app`  
**Middleware**: `auth`

**Structure** (Lightweight View):
```blade
@extends('layouts.app')

@section('title', __('items.title') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            @include('items._partials.page-header', ['items' => $items])
            
            <main class="khezana-listing-main">
                @if ($items->count() > 0)
                    @include('items._partials.grid', ['items' => $items])
                    @include('items._partials.pagination', ['items' => $items])
                @else
                    @include('items._partials.empty-state')
                @endif
            </main>
        </div>
    </div>
@endsection
```

**Features**:
- User's own items only
- Create new item button
- Item cards with actions
- Pagination
- Empty state with CTA

**Data Passed**:
- `$items` - User's paginated items (Item model)
- `$sort` - Current sort option

#### 2. User Profile (`profile/`)

**Routes**:
- `/profile` - Profile overview (`profile.show`)
- `/profile/edit` - Edit profile (`profile.edit`)
- `/profile/password` - Security settings (`profile.password`)

**Controller**: `ProfileController`

**Features**:
- Uses `ProfileViewModel` for presentation logic
- Navigation sidebar
- Profile information display
- Edit form with validation
- Password update with security tips
- Account deletion

**Structure**:
```blade
@extends('layouts.app')

@section('title', __('profile.title') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-profile-page">
        <div class="khezana-container">
            <!-- Breadcrumb -->
            <!-- Profile Header -->
            <!-- Profile Content -->
            <div class="khezana-profile-content">
                <div class="khezana-profile-sidebar">
                    @include('profile._partials.navigation')
                </div>
                <main class="khezana-profile-main">
                    <!-- Profile sections -->
                </main>
            </div>
        </div>
    </div>
@endsection
```

**ViewModel**: `ProfileViewModel::fromUser($user)`

---

## ViewModel Pattern

### Overview

The ViewModel Pattern separates presentation logic from Blade views, making views simple and testable.

### Architecture

```
Controller → ViewModel → View
```

1. **Controller** creates ViewModel from model
2. **ViewModel** prepares all presentation data
3. **View** receives ready-to-display data

### ViewModels

#### 1. ItemCardViewModel (`app/ViewModels/Items/ItemCardViewModel.php`)

**Purpose**: Encapsulates all data and logic for item card display

**Methods**:
- `fromItem($item, $variant)` - Create from Item model
- `fromReadModel($readModel, $variant)` - Create from ItemReadModel
- `getImageUrl()` - Get primary image URL
- `getDisplayPrice()` - Get formatted price
- `getOperationTypeLabel()` - Get operation type label
- `toArray()` - Convert to array for Blade

**Usage**:
```php
// In Controller
$viewModel = ItemCardViewModel::fromItem($item, 'user');

// In Blade
{{ $viewModel->title }}
{{ $viewModel->getDisplayPrice() }}
```

#### 2. ItemDetailViewModel (`app/ViewModels/Items/ItemDetailViewModel.php`)

**Purpose**: Encapsulates all data and logic for item detail page

**Properties**:
- `id`, `title`, `description`
- `price`, `depositAmount`
- `images`, `primaryImage`
- `approvalStatus`, `isPending`, `isApproved`
- `canEdit`, `canDelete`, `canSubmitForApproval`
- And more...

**Methods**:
- `fromItem($item, $variant)` - Create from Item model
- `toArray()` - Convert to array

**Usage**:
```php
// In Controller
$viewModel = ItemDetailViewModel::fromItem($item, 'user');
return view('items.show', ['viewModel' => $viewModel]);

// In Blade
{{ $viewModel->title }}
@if($viewModel->canEdit)
    <a href="{{ $viewModel->editUrl }}">Edit</a>
@endif
```

#### 3. ProfileViewModel (`app/ViewModels/Profile/ProfileViewModel.php`)

**Purpose**: Encapsulates all data and logic for profile pages

**Methods**:
- `fromUser($user)` - Create from User model
- `isEmailVerified()` - Check email verification
- `getEmailVerificationStatus()` - Get verification status text
- `getMemberSinceFormatted()` - Get formatted member since date
- `toArray()` - Convert to array

**Usage**:
```php
// In Controller
$viewModel = ProfileViewModel::fromUser($request->user());
return view('profile.show', ['viewModel' => $viewModel]);
```

### Benefits

1. **Separation of Concerns** - Logic separated from presentation
2. **Testability** - ViewModels can be unit tested
3. **Reusability** - Same ViewModel can be used in multiple views
4. **Maintainability** - Changes to logic don't affect views
5. **Type Safety** - Readonly properties ensure data integrity

---

## Partials & Components

### Global Partials (`partials/`)

#### 1. Header (`partials/header.blade.php`)

**Purpose**: Main site navigation

**Components**:
- Logo (with fallback)
- Mobile menu toggle
- Navigation links:
  - Offers dropdown (Sell, Rent, Donate)
  - Requests link
  - My Items (authenticated)
  - My Requests (authenticated)
- User actions:
  - Login/Register (guest)
  - Add Item button
  - User dropdown (authenticated)

**Features**:
- Responsive mobile menu
- Dropdown menus
- Conditional links based on auth status
- RTL support

#### 2. Footer (`partials/footer.blade.php`)

**Purpose**: Site footer

**Sections**:
- App description
- Quick links (Sell, Rent, Donate, Requests)
- Information links (How It Works, Fees, Terms, Privacy)
- Trust indicators
- Copyright

#### 3. Item Card (`partials/item-card.blade.php`)

**Purpose**: Reusable item card component (Dumb Component)

**Features**:
- Pure presentation component
- Receives data via props
- No internal logic
- Supports multiple variants: `public`, `user`, `compact`
- Uses `ItemCardViewModel` for data preparation

**Props**:
```blade
@include('partials.item-card', [
    'itemId' => $itemId,
    'variant' => 'public', // 'public', 'user', or 'compact'
    'url' => $url,
    'title' => $title,
    'imageUrl' => $imageUrl,
    'displayPrice' => $displayPrice,
    'operationType' => $operationType,
    // ... more props
])
```

**Usage with Helper**:
```php
// In Controller or Helper
$props = ItemCardHelper::preparePublicItem($item);
// Returns array of props ready for item-card

// In Blade
@include('partials.item-card', ItemCardHelper::preparePublicItem($item))
```

**BEM Naming**:
- `.khezana-item-card` - Base class
- `.khezana-item-card--public` - Public variant
- `.khezana-item-card--user` - User variant
- `.khezana-item-card--compact` - Compact variant

### Feature-Specific Partials

Each feature has its own `_partials/` directory:

#### Items Partials (`items/_partials/`)

- `page-header.blade.php` - Page header with title, count, create button
- `grid.blade.php` - Items grid using item-card component
- `empty-state.blade.php` - Empty state with CTA
- `pagination.blade.php` - Pagination links
- `detail/` - Detail page partials (9 files)

#### Requests Partials (`requests/_partials/`)

- `page-header.blade.php`
- `grid.blade.php` - Uses `RequestCardViewModel`
- `empty-state.blade.php`
- `pagination.blade.php`

#### Profile Partials (`profile/_partials/`)

- `navigation.blade.php` - Profile navigation sidebar

---

## CSS Architecture

### Component-Based & Page-Based Structure

The CSS follows a **modular, component-based and page-based architecture**:

```
home.css (Main Entry Point)
├── variables.css              # Design tokens
├── base.css                   # Reset & typography
├── layout.css                 # Grid & containers
├── header.css                 # Navigation
├── buttons.css                # Button components
├── hero.css                  # Hero section
├── sections.css              # Page sections
├── forms.css                 # Form elements
├── footer.css                # Footer
├── auth.css                  # Auth pages
├── responsive-improvements.css
├── components/               # Component-based CSS
│   ├── item-card.css        # Item card styles
│   ├── pagination.css       # Pagination styles
│   └── empty-state.css      # Empty state styles
└── pages/                    # Page-based CSS
    ├── items-index.css      # Items listing page
    ├── items-show.css       # Item detail page
    ├── requests-index.css   # Requests listing page
    ├── static-pages.css     # Static pages
    └── profile.css          # Profile pages
```

### Design Tokens (`variables.css`)

**Colors**:
```css
--khezana-primary: #f59e0b;        /* Amber */
--khezana-primary-dark: #d97706;
--khezana-primary-light: #fbbf24;
--khezana-secondary: #6b7280;
--khezana-success: #10b981;
--khezana-danger: #ef4444;
--khezana-text: #1f2937;
--khezana-text-light: #6b7280;
--khezana-bg: #faf8f5;
--khezana-white: #ffffff;
--khezana-border: #e5e7eb;
```

**Spacing**:
```css
--khezana-spacing-xs: 0.5rem;
--khezana-spacing-sm: 1rem;
--khezana-spacing-md: 1.5rem;
--khezana-spacing-lg: 2rem;
--khezana-spacing-xl: 3rem;
--khezana-spacing-2xl: 4rem;
```

**Typography**:
```css
--khezana-font-size-base: 1rem;
--khezana-font-size-sm: 0.875rem;
--khezana-font-size-lg: 1.125rem;
--khezana-font-size-xl: 1.25rem;
--khezana-font-size-2xl: 1.5rem;
--khezana-font-size-3xl: 1.875rem;
--khezana-font-size-4xl: 2.25rem;
```

### BEM Naming Convention

All CSS classes follow BEM (Block Element Modifier) pattern:

**Pattern**: `khezana-{block}__{element}--{modifier}`

**Examples**:
- `.khezana-item-card` - Block
- `.khezana-item-card__image` - Element
- `.khezana-item-card__image--primary` - Modifier
- `.khezana-item-card--public` - Block modifier
- `.khezana-item-card--compact` - Block modifier

**RTL Support**:
- Uses logical properties: `margin-inline-start`, `padding-inline-end`
- `[dir="rtl"]` selectors for RTL-specific styles
- `flex-direction: row-reverse` for RTL layouts

### Component Files

#### `components/item-card.css`
- Item card base styles
- Variants: `--public`, `--user`, `--compact`
- Image handling
- Price display
- Hover effects
- RTL support

#### `components/pagination.css`
- Pagination container
- Page links
- Active state
- RTL support

#### `components/empty-state.css`
- Empty state container
- Icon/illustration
- Message text
- CTA button
- RTL support

### Page Files

#### `pages/items-index.css`
- Listing page layout
- Page header styles
- Grid container
- RTL support

#### `pages/items-show.css`
- Detail page layout
- Breadcrumb styles
- Image gallery
- Meta information
- RTL support

#### `pages/profile.css`
- Profile page layout
- Profile header
- Navigation sidebar
- Profile cards
- Form styles
- RTL support

#### `pages/static-pages.css`
- Static page layout
- Section styles
- Process steps (How It Works)
- Fee cards (Fees page)
- RTL support

---

## How It All Works Together

### Request Flow

1. **User visits URL** → Route defined in `routes/web.php`
2. **Route calls Controller** → Controller method executes
3. **Controller creates ViewModel** → From model/read model
4. **Controller returns view** → `return view('view.name', ['viewModel' => $viewModel])`
5. **View extends layout** → `@extends('layouts.app')`
6. **Layout includes partials** → Header, Footer
7. **View includes feature partials** → Grid, Pagination, etc.
8. **Partials use components** → item-card, etc.
9. **CSS loads** → Via `home.css` import chain
10. **JavaScript executes** → Via `@stack('scripts')` or inline

### Example: Public Item Listing

```
User Request: GET /items
    ↓
Route: public.items.index
    ↓
Controller: Public\ItemController@index
    ↓
Query: BrowseItemsQuery (gets approved items)
    ↓
Returns: Paginated ItemReadModel collection
    ↓
View: public.items.index (lightweight)
    ↓
Includes: public.items._partials.grid
    ↓
Grid includes: partials.item-card (for each item)
    ↓
Item Card uses: ItemCardHelper::preparePublicItem()
    ↓
Creates: ItemCardViewModel
    ↓
Renders: HTML with CSS classes
    ↓
CSS: home.css → imports all modules
    ↓
Browser: Displays styled page
```

### Data Flow with ViewModel

**Controller**:
```php
public function show(Item $item): View
{
    $viewModel = ItemDetailViewModel::fromItem($item, 'user');
    return view('items.show', ['viewModel' => $viewModel]);
}
```

**View**:
```blade
@extends('layouts.app')

@section('title', $viewModel->title . ' - ' . config('app.name'))

@section('content')
    <h1>{{ $viewModel->title }}</h1>
    <p>{{ $viewModel->description }}</p>
    @if($viewModel->canEdit)
        <a href="{{ $viewModel->editUrl }}">Edit</a>
    @endif
@endsection
```

**ViewModel** (handles all logic):
```php
class ItemDetailViewModel
{
    public function __construct(
        public readonly string $title,
        public readonly bool $canEdit,
        public readonly string $editUrl,
        // ...
    ) {}
    
    public static function fromItem(Item $item, string $variant): self
    {
        return new self(
            title: $item->title,
            canEdit: $item->user_id === auth()->id() && !$item->isPending(),
            editUrl: route('items.edit', $item),
            // ...
        );
    }
}
```

---

## Examples & Usage

### Creating a New Feature

**Step 1: Create Directory Structure**
```bash
mkdir -p resources/views/my-feature/_partials
```

**Step 2: Create Partials**
- `page-header.blade.php`
- `grid.blade.php`
- `empty-state.blade.php`
- `pagination.blade.php`

**Step 3: Create Main View**
```blade
{{-- resources/views/my-feature/index.blade.php --}}
@extends('layouts.app')

@section('title', __('my-feature.title') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            @include('my-feature._partials.page-header', ['items' => $items])
            
            <main class="khezana-listing-main">
                @if ($items->count() > 0)
                    @include('my-feature._partials.grid', ['items' => $items])
                    @include('my-feature._partials.pagination', ['items' => $items])
                @else
                    @include('my-feature._partials.empty-state')
                @endif
            </main>
        </div>
    </div>
@endsection
```

**Step 4: Create ViewModel (Optional)**
```php
// app/ViewModels/MyFeature/MyFeatureCardViewModel.php
class MyFeatureCardViewModel
{
    public static function fromModel($model): self
    {
        return new self(
            // Prepare data
        );
    }
}
```

**Step 5: Create CSS**
```css
/* public/css/pages/my-feature-index.css */
.khezana-my-feature-page {
    /* Styles */
}
```

**Step 6: Import CSS**
```css
/* public/css/home.css */
@import url('pages/my-feature-index.css');
```

### Using ViewModels

**In Controller**:
```php
public function index(): View
{
    $items = Item::where('user_id', auth()->id())->paginate();
    
    return view('items.index', [
        'items' => $items->through(fn($item) => ItemCardViewModel::fromItem($item, 'user')),
    ]);
}
```

**In Blade**:
```blade
@foreach ($items as $viewModel)
    <div class="khezana-item-card">
        <h3>{{ $viewModel->title }}</h3>
        <p>{{ $viewModel->getDisplayPrice() }}</p>
    </div>
@endforeach
```

### Using Partials

**Include a Partial**:
```blade
@include('items._partials.page-header', [
    'items' => $items,
    'title' => __('items.title'),
    'showCreateButton' => true,
])
```

**Conditional Include**:
```blade
@auth
    @include('partials.user-menu')
@else
    @include('partials.guest-menu')
@endauth
```

### Adding Custom Styles

**Option 1: Using @stack**
```blade
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endpush
```

**Option 2: Inline Styles**
```blade
@push('styles')
    <style>
        .custom-class {
            color: var(--khezana-primary);
        }
    </style>
@endpush
```

### Working with Translations

**In Views**:
```blade
{{ __('common.ui.home') }}
{{ __('items.title') }}
{{ __('items.operation_types.sell') }}
```

**With Parameters**:
```blade
{{ __('common.ui.welcome', ['name' => $user->name]) }}
```

### Form Handling

**Create Form**:
```blade
<form method="POST" action="{{ route('items.store') }}">
    @csrf
    
    <div class="khezana-form-group">
        <label for="title" class="khezana-form-label">
            {{ __('items.fields.title') }}
            <span class="khezana-required">*</span>
        </label>
        <input type="text" 
               id="title"
               name="title" 
               class="khezana-form-input @error('title') khezana-form-input--error @enderror"
               value="{{ old('title') }}"
               required>
        @error('title')
            <span class="khezana-form-error">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="khezana-form-actions">
        <button type="submit" class="khezana-btn khezana-btn--primary">
            {{ __('common.actions.save') }}
        </button>
        <a href="{{ route('items.index') }}" class="khezana-btn khezana-btn--secondary">
            {{ __('common.actions.cancel') }}
        </a>
    </div>
</form>
```

---

## Best Practices

### 1. Always Use ViewModels for Complex Logic
```blade
❌ Bad: 
@php
    $canEdit = $item->user_id === auth()->id() && !$item->isPending();
    $editUrl = route('items.edit', $item);
@endphp

✅ Good:
{{-- Controller creates ViewModel --}}
@if($viewModel->canEdit)
    <a href="{{ $viewModel->editUrl }}">Edit</a>
@endif
```

### 2. Use Translations
```blade
❌ Bad: <h1>Home</h1>
✅ Good: <h1>{{ __('common.ui.home') }}</h1>
```

### 3. Use Design System Classes
```blade
❌ Bad: <button style="background: #f59e0b;">Click</button>
✅ Good: <button class="khezana-btn khezana-btn--primary">Click</button>
```

### 4. Keep Views Lightweight
```blade
❌ Bad: Complex logic in Blade
@php
    // 50 lines of logic
@endphp

✅ Good: Use ViewModels and Partials
@include('items._partials.grid', ['items' => $items])
```

### 5. Use Route Names
```blade
❌ Bad: <a href="/items">Items</a>
✅ Good: <a href="{{ route('public.items.index') }}">Items</a>
```

### 6. Handle Empty States
```blade
@if ($items->count() > 0)
    @include('items._partials.grid', ['items' => $items])
    @include('items._partials.pagination', ['items' => $items])
@else
    @include('items._partials.empty-state')
@endif
```

### 7. Use Partials for Reusability
```blade
❌ Bad: Copy-paste card HTML everywhere
✅ Good: @include('partials.item-card', ItemCardHelper::preparePublicItem($item))
```

### 8. Add SEO Meta Tags
```blade
@push('meta')
    <meta name="description" content="{{ __('items.meta_description') }}">
    <link rel="canonical" href="{{ route('public.items.index') }}">
    <meta property="og:title" content="{{ __('items.title') }}">
@endpush
```

### 9. Use Semantic HTML
```blade
<article class="khezana-item-card">
    <header class="khezana-item-card__header">
        <h2>{{ $viewModel->title }}</h2>
    </header>
    <main class="khezana-item-card__content">
        <!-- Content -->
    </main>
</article>
```

### 10. Support RTL
```css
/* Use logical properties */
margin-inline-start: var(--khezana-spacing-md);
padding-inline-end: var(--khezana-spacing-sm);

/* RTL-specific styles */
[dir="rtl"] .khezana-item-card {
    flex-direction: row-reverse;
}
```

---

## Summary

The Khezana Blade views system is built on:

1. **Feature-Based Organization** - Views organized by feature
2. **ViewModel Pattern** - Separation of presentation logic
3. **Component-Based Partials** - Reusable UI components
4. **Modular CSS** - Component-based and page-based architecture
5. **Layout System** - Base layouts for different page types
6. **RTL Support** - Full Arabic language support
7. **SEO-Friendly** - Meta tags, semantic HTML
8. **Responsive Design** - Mobile-first approach
9. **Accessibility** - ARIA labels and semantic HTML

This architecture ensures:
- ✅ Maintainability
- ✅ Reusability
- ✅ Consistency
- ✅ Scalability
- ✅ Performance
- ✅ Testability
- ✅ Separation of Concerns

---

**Last Updated**: January 2026  
**Version**: 3.0 (Feature-Based + ViewModel Pattern)  
**Author**: Khezana Development Team
