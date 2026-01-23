# Khezana - Blade Views & Interfaces Documentation

## 📋 Table of Contents

1. [Overview](#overview)
2. [File Structure](#file-structure)
3. [Layout System](#layout-system)
4. [Views Organization](#views-organization)
5. [Partials & Components](#partials--components)
6. [CSS Architecture](#css-architecture)
7. [How It All Works Together](#how-it-all-works-together)
8. [Examples & Usage](#examples--usage)

---

## Overview

The Khezana project uses **Laravel Blade** templating engine to build a modern, RTL (Right-to-Left) Arabic-first interface. The architecture follows a modular approach with:

- **Layout-based structure** - Base layouts that pages extend
- **Component-based partials** - Reusable UI components
- **Modular CSS** - Organized CSS files by functionality
- **Separation of concerns** - Public vs Authenticated views
- **RTL support** - Full Arabic language support

### Key Technologies

- **Laravel 12.47.0** - PHP Framework
- **Blade Templates** - Templating engine
- **Custom CSS** - Modular design system
- **Vite** - Asset bundling
- **Tailwind CSS** - Utility classes (optional)

---

## File Structure

```
resources/views/
├── auth/                          # Authentication pages
│   ├── login.blade.php
│   └── register.blade.php
│
├── filament/                      # Admin panel custom pages
│   └── pages/
│       └── platform-settings.blade.php
│
├── home/                          # Homepage
│   └── index.blade.php
│
├── items/                         # User's own items (authenticated)
│   ├── create.blade.php          # Create new item form
│   ├── edit.blade.php            # Edit item form
│   ├── index.blade.php           # User's items listing
│   └── show.blade.php            # Item detail page
│
├── layouts/                       # Base layouts
│   ├── app.blade.php             # Main application layout
│   ├── auth.blade.php            # Authentication layout
│   └── home.blade.php            # Homepage layout
│
├── partials/                      # Reusable components
│   ├── footer.blade.php          # Site footer
│   ├── header.blade.php          # Site header/navigation
│   └── item-card.blade.php       # Item card component
│
├── public/                        # Public-facing pages
│   ├── items/
│   │   ├── index.blade.php       # Public items listing
│   │   └── show.blade.php       # Public item detail
│   └── requests/
│       ├── create-info.blade.php # Request info page
│       ├── index.blade.php       # Public requests listing
│       └── show.blade.php       # Public request detail
│
└── requests/                      # User's own requests (authenticated)
    ├── create.blade.php          # Create request form
    ├── index.blade.php           # User's requests listing
    └── show.blade.php            # Request detail page

public/css/
├── home.css                      # Main CSS entry point (imports all)
├── variables.css                 # Design tokens & CSS variables
├── base.css                      # Reset & base styles
├── layout.css                    # Layout & grid systems
├── header.css                    # Header & navigation
├── buttons.css                   # Button components
├── hero.css                      # Hero section
├── sections.css                  # Page sections
├── forms.css                     # Form elements
├── cards.css                     # Card components
├── listing.css                   # Listing pages
├── detail.css                    # Detail pages
├── modals.css                    # Modal dialogs
├── footer.css                    # Footer
├── auth.css                      # Authentication pages
├── requests.css                  # Request pages
└── utilities.css                 # Utility classes
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
    <!-- Meta tags -->
    <!-- Fonts (Figtree) -->
    <!-- Styles (Vite + Custom CSS) -->
    @stack('styles')
</head>
<body>
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

**Usage**:
```blade
@extends('layouts.app')

@section('title', 'Page Title')

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

---

## Views Organization

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

**Structure**:
```blade
- Page Header (title, count, actions)
- Items Grid (using item-card partial)
- Pagination
- Empty State (if no items)
```

**Data Passed**:
- `$items` - Paginated items collection
- `$sort` - Current sort option

#### 3. Public Item Detail (`public/items/show.blade.php`)

**Route**: `/items/{id}/{slug?}`  
**Controller**: `Public\ItemController@show`  
**Layout**: `layouts.app`

**Features**:
- Image gallery with zoom
- Full item details
- Contact form (authenticated users)
- Breadcrumb navigation
- Keyboard navigation (arrows, ESC)
- Touch gestures for mobile

**JavaScript Features**:
- Image gallery with modal
- Thumbnail navigation
- Fullscreen image viewer
- Contact form toggle

#### 4. Public Requests Listing (`public/requests/index.blade.php`)

**Route**: `/requests`  
**Controller**: `Public\RequestController@index`  
**Layout**: `layouts.app`

**Structure**:
- Page header
- Requests grid
- Pagination
- Empty state

#### 5. Public Request Detail (`public/requests/show.blade.php`)

**Route**: `/requests/{id}/{slug?}`  
**Controller**: `Public\RequestController@show`  
**Layout**: `layouts.app`

**Features**:
- Request details
- Offer submission
- Contact functionality
- Status badges

### Authenticated Views (User's Own Content)

#### 1. User Items Listing (`items/index.blade.php`)

**Route**: `/my-items`  
**Controller**: `ItemController@index`  
**Layout**: `layouts.app`  
**Middleware**: `auth`

**Features**:
- User's own items only
- Create new item button
- Item cards with actions
- Pagination
- Empty state with CTA

**Data Passed**:
- `$items` - User's paginated items
- `$sort` - Current sort option

#### 2. Create Item (`items/create.blade.php`)

**Route**: `/my-items/create`  
**Controller**: `ItemController@create`  
**Layout**: `layouts.app`  
**Middleware**: `auth`

**Features**:
- Pre-creation notice modal
- Dynamic category attributes
- Image upload
- Operation type selection
- Price calculation with fees

**Form Fields**:
- Category (with dynamic attributes)
- Operation type (sell/rent/donate)
- Title
- Description
- Condition
- Price
- Deposit amount
- Images
- Attributes (dynamic based on category)

#### 3. Edit Item (`items/edit.blade.php`)

**Route**: `/my-items/{item}/edit`  
**Controller**: `ItemController@edit`  
**Layout**: `layouts.app`  
**Middleware**: `auth`

**Similar to create but with pre-filled data**

#### 4. Item Detail (`items/show.blade.php`)

**Route**: `/my-items/{item}`  
**Controller**: `ItemController@show`  
**Layout**: `layouts.app`  
**Middleware**: `auth`

**Features**:
- Full item details
- Approval status display
- Edit button
- Delete button (with modal)
- Submit for approval
- Image gallery

**Delete Modal Features**:
- Soft delete option
- Archive option
- Hard delete (admin only)
- Confirmation required
- Reason field (admin)

#### 5. User Requests (`requests/index.blade.php`)

**Route**: `/my-requests`  
**Controller**: `RequestController@index`  
**Layout**: `layouts.app`  
**Middleware**: `auth`

**Similar structure to items listing**

---

## Partials & Components

### 1. Header (`partials/header.blade.php`)

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

### 2. Footer (`partials/footer.blade.php`)

**Purpose**: Site footer

**Sections**:
- App description
- Quick links
- Information links
- Trust indicators
- Copyright

### 3. Item Card (`partials/item-card.blade.php`)

**Purpose**: Reusable item card component

**Features**:
- Works with both public and user items
- Image with lazy loading
- Multiple image indicator
- Hover preview (desktop)
- Skeleton loader
- Price display with fees
- Operation type badge
- Condition & category info
- Responsive design

**Usage**:
```blade
@include('partials.item-card', ['item' => $item])
```

**Smart Detection**:
- Automatically detects if item is public (ItemReadModel) or user item (Item model)
- Adjusts URLs and data access accordingly

---

## CSS Architecture

### Design System Structure

The CSS follows a **modular, component-based architecture**:

```
home.css (Main Entry Point)
├── variables.css      # Design tokens
├── base.css           # Reset & typography
├── layout.css         # Grid & containers
├── header.css         # Navigation
├── buttons.css        # Button components
├── hero.css           # Hero section
├── sections.css       # Page sections
├── forms.css          # Form elements
├── cards.css          # Card components
├── listing.css        # Listing pages
├── detail.css         # Detail pages
├── modals.css         # Modals
├── footer.css         # Footer
├── auth.css           # Auth pages
├── requests.css       # Request pages
└── utilities.css      # Utilities
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

**Shadows**:
```css
--khezana-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
--khezana-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
--khezana-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
--khezana-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
```

### Naming Convention

All CSS classes follow the pattern: `khezana-{component}-{element}-{modifier}`

**Examples**:
- `khezana-btn` - Base button
- `khezana-btn-primary` - Primary button
- `khezana-btn-large` - Large button
- `khezana-item-card` - Item card
- `khezana-item-card-modern` - Modern variant
- `khezana-form-input` - Form input
- `khezana-form-error` - Form error message

### Component Files

#### `buttons.css`
- Button variants (primary, secondary, danger)
- Button sizes (small, default, large)
- Button states (hover, active, disabled)
- CTA buttons

#### `cards.css`
- Item cards
- Service cards
- Request cards
- Card hover effects
- Image containers

#### `forms.css`
- Form inputs
- Textareas
- Selects
- Checkboxes/radios
- Form groups
- Error messages
- Validation states

#### `listing.css`
- Page headers
- Grid layouts
- Pagination
- Empty states
- Filter sections (removed)

#### `detail.css`
- Item detail pages
- Image galleries
- Breadcrumbs
- Meta information
- Action buttons

#### `modals.css`
- Modal overlays
- Modal content
- Delete confirmation modals
- Image modals
- Form modals

---

## How It All Works Together

### Request Flow

1. **User visits URL** → Route defined in `routes/web.php`
2. **Route calls Controller** → Controller method executes
3. **Controller fetches data** → From database/models
4. **Controller returns view** → `return view('view.name', $data)`
5. **View extends layout** → `@extends('layouts.app')`
6. **Layout includes partials** → Header, Footer
7. **View renders content** → Using Blade syntax
8. **CSS loads** → Via `home.css` import chain
9. **JavaScript executes** → Via `@stack('scripts')` or inline

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
View: public.items.index
    ↓
Layout: layouts.app
    ├── Includes: partials.header
    ├── Yields: content (items listing)
    └── Includes: partials.footer
    ↓
Renders: HTML with CSS classes
    ↓
CSS: home.css → imports all modules
    ↓
Browser: Displays styled page
```

### Data Flow

**Controller → View**:
```php
// Controller
return view('public.items.index', [
    'items' => $items,  // Paginated collection
    'sort' => $sort,    // Current sort
]);
```

**View Usage**:
```blade
@foreach ($items as $item)
    @include('partials.item-card', ['item' => $item])
@endforeach
```

**Partial Processing**:
```blade
@php
    // Detect item type (public vs user)
    $isPublicItem = isset($item->primaryImage);
    // Build appropriate URL
    $itemUrl = $isPublicItem 
        ? route('public.items.show', ['id' => $item->id])
        : route('items.show', $item);
@endphp
```

---

## Examples & Usage

### Creating a New Page

**Step 1: Create Route**
```php
// routes/web.php
Route::get('/about', [AboutController::class, 'index'])->name('about');
```

**Step 2: Create Controller Method**
```php
// app/Http/Controllers/AboutController.php
public function index()
{
    return view('about.index');
}
```

**Step 3: Create View**
```blade
{{-- resources/views/about/index.blade.php --}}
@extends('layouts.app')

@section('title', __('common.ui.about_us') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-container">
        <h1 class="khezana-page-title">{{ __('common.ui.about_us') }}</h1>
        <p>Content here...</p>
    </div>
@endsection
```

### Using Partials

**Include a Partial**:
```blade
@include('partials.item-card', [
    'item' => $item,
    'showActions' => true
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
{{-- In your view --}}
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

### Adding JavaScript

**Option 1: Using @stack**
```blade
@push('scripts')
    <script>
        // Your JavaScript
    </script>
@endpush
```

**Option 2: External File**
```blade
@push('scripts')
    <script src="{{ asset('js/custom.js') }}"></script>
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
        <label class="khezana-form-label">
            {{ __('items.fields.title') }}
            <span class="khezana-required">*</span>
        </label>
        <input type="text" 
               name="title" 
               class="khezana-form-input"
               value="{{ old('title') }}"
               required>
        @error('title')
            <span class="khezana-form-error">{{ $message }}</span>
        @enderror
    </div>
    
    <button type="submit" class="khezana-btn khezana-btn-primary">
        {{ __('common.actions.save') }}
    </button>
</form>
```

### Displaying Data

**Loops**:
```blade
@foreach ($items as $item)
    <div class="khezana-item-card">
        <h3>{{ $item->title }}</h3>
        <p>{{ $item->description }}</p>
    </div>
@endforeach
```

**Conditionals**:
```blade
@if ($item->price)
    <div class="khezana-item-price">
        {{ number_format($item->price, 0) }} {{ __('common.ui.currency') }}
    </div>
@else
    <div class="khezana-item-price-free">
        {{ __('common.ui.free') }}
    </div>
@endif
```

**Pagination**:
```blade
@if ($items->hasPages())
    <div class="khezana-pagination">
        {{ $items->appends(request()->query())->links() }}
    </div>
@endif
```

---

## Best Practices

### 1. Always Use Translations
```blade
❌ Bad: <h1>Home</h1>
✅ Good: <h1>{{ __('common.ui.home') }}</h1>
```

### 2. Use Design System Classes
```blade
❌ Bad: <button style="background: #f59e0b;">Click</button>
✅ Good: <button class="khezana-btn khezana-btn-primary">Click</button>
```

### 3. Include CSRF Tokens
```blade
<form method="POST">
    @csrf
    <!-- form fields -->
</form>
```

### 4. Use Route Names
```blade
❌ Bad: <a href="/items">Items</a>
✅ Good: <a href="{{ route('public.items.index') }}">Items</a>
```

### 5. Handle Empty States
```blade
@if ($items->count() > 0)
    <!-- Display items -->
@else
    <div class="khezana-empty-state">
        <p>{{ __('common.ui.no_items') }}</p>
    </div>
@endif
```

### 6. Use Partials for Reusability
```blade
❌ Bad: Copy-paste card HTML everywhere
✅ Good: @include('partials.item-card', ['item' => $item])
```

### 7. Add Accessibility
```blade
<button aria-label="{{ __('common.ui.close') }}">
    <span aria-hidden="true">×</span>
</button>
```

### 8. Optimize Images
```blade
<img src="{{ asset('storage/' . $image->path) }}" 
     alt="{{ $item->title }}"
     loading="lazy">
```

---

## Troubleshooting

### Common Issues

**1. View Not Found**
- Check file path matches namespace
- Clear view cache: `php artisan view:clear`

**2. CSS Not Loading**
- Check `public/css/home.css` exists
- Verify asset path: `{{ asset('css/home.css') }}`
- Clear cache: `php artisan cache:clear`

**3. Translations Missing**
- Check language file exists: `lang/ar/common.php`
- Verify translation key exists
- Clear cache: `php artisan config:clear`

**4. Partials Not Rendering**
- Check partial path: `partials/header.blade.php`
- Verify `@include` syntax
- Check for PHP errors in partial

**5. Layout Not Extending**
- Verify `@extends` is first line
- Check layout file exists
- Ensure no syntax errors before `@extends`

---

## Summary

The Khezana Blade views system is built on:

1. **Modular Layouts** - Base layouts for different page types
2. **Reusable Partials** - Components like header, footer, item-card
3. **Organized Views** - Separated by public/authenticated and feature
4. **Design System** - Consistent CSS architecture with design tokens
5. **RTL Support** - Full Arabic language support
6. **Responsive Design** - Mobile-first approach
7. **Accessibility** - ARIA labels and semantic HTML

This architecture ensures:
- ✅ Maintainability
- ✅ Reusability
- ✅ Consistency
- ✅ Scalability
- ✅ Performance

---

**Last Updated**: January 2026  
**Version**: 1.0  
**Author**: Khezana Development Team
