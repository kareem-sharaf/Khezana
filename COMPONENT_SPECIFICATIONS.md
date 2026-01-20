# Component Specifications - Khezana Marketplace
## Detailed Component Implementation Guide

**Purpose:** Technical specifications for each UI component  
**Target:** Frontend developers implementing Blade components

---

## 1. Button Component

### File: `resources/views/components/button.blade.php`

### Props:
```php
type: 'primary' | 'secondary' | 'ghost' (default: 'primary')
size: 'small' | 'medium' | 'large' (default: 'medium')
disabled: bool (default: false)
href: string|null (optional, for link buttons)
```

### Usage Examples:
```blade
{{-- Primary Button --}}
<x-button type="primary">إضافة إعلان</x-button>

{{-- Secondary Button --}}
<x-button type="secondary">إلغاء</x-button>

{{-- Link Button --}}
<x-button type="primary" href="/items/create">إنشاء إعلان</x-button>

{{-- Disabled Button --}}
<x-button type="primary" :disabled="true">غير متاح</x-button>

{{-- Large Button --}}
<x-button type="primary" size="large">تسجيل الدخول</x-button>
```

### HTML Structure:
```html
<button class="btn btn-{{ $type }} btn-{{ $size }}" {{ $disabled ? 'disabled' : '' }}>
    {{ $slot }}
</button>
```

### CSS Classes:
- `.btn`: Base button styles
- `.btn-primary`: Primary button styles
- `.btn-secondary`: Secondary button styles
- `.btn-ghost`: Ghost button styles
- `.btn-small`, `.btn-medium`, `.btn-large`: Size variants
- `.btn:disabled`: Disabled state

---

## 2. Badge Component

### File: `resources/views/components/badge.blade.php`

### Props:
```php
type: 'sell' | 'rent' | 'donate' | 'request' | 'available' | 'pending' | 'closed' | 'approved'
```

### Usage Examples:
```blade
{{-- Operation Type Badges --}}
<x-badge type="sell">بيع</x-badge>
<x-badge type="rent">تأجير</x-badge>
<x-badge type="donate">تبرع</x-badge>
<x-badge type="request">طلب</x-badge>

{{-- Status Badges --}}
<x-badge type="available">متاح</x-badge>
<x-badge type="pending">قيد المراجعة</x-badge>
<x-badge type="closed">مغلق</x-badge>
```

### HTML Structure:
```html
<span class="badge badge-{{ $type }}">
    {{ $slot }}
</span>
```

### CSS Classes:
- `.badge`: Base badge styles
- `.badge-sell`, `.badge-rent`, etc.: Type-specific colors

---

## 3. Item Card Component

### File: `resources/views/components/items/item-card.blade.php`

### Props:
```php
item: ItemReadModel (required)
showCategory: bool (default: true)
showLocation: bool (default: true)
showDate: bool (default: true)
```

### Usage:
```blade
<x-items.item-card :item="$item" />
```

### HTML Structure:
```html
<article class="item-card">
    <a href="{{ route('public.items.show', ['id' => $item->id, 'slug' => $item->slug]) }}" class="item-card-link">
        <div class="item-card-image">
            <img 
                src="{{ $item->images->first()?->path ?? '/images/placeholder.jpg' }}" 
                alt="{{ $item->title }}"
                loading="lazy"
            />
        </div>
        <div class="item-card-content">
            <div class="item-card-badge">
                <x-badge :type="$item->operationType->value">
                    {{ $item->operationType->label() }}
                </x-badge>
            </div>
            <h3 class="item-card-title">{{ $item->title }}</h3>
            <div class="item-card-price">{{ $item->getFormattedPrice() }}</div>
            @if($showCategory && $item->category)
                <div class="item-card-category">{{ $item->category->name }}</div>
            @endif
            @if($showDate)
                <div class="item-card-date">{{ $item->createdAt->diffForHumans() }}</div>
            @endif
        </div>
    </a>
</article>
```

### CSS Classes:
- `.item-card`: Card container
- `.item-card-link`: Link wrapper (no underline)
- `.item-card-image`: Image container
- `.item-card-content`: Content wrapper
- `.item-card-badge`: Badge container
- `.item-card-title`: Title (H3)
- `.item-card-price`: Price (bold, primary color)
- `.item-card-category`: Category text
- `.item-card-date`: Date text (caption)

---

## 4. Request Card Component

### File: `resources/views/components/requests/request-card.blade.php`

### Props:
```php
request: RequestReadModel (required)
showCategory: bool (default: true)
showOffersCount: bool (default: true)
showDate: bool (default: true)
```

### Usage:
```blade
<x-requests.request-card :request="$request" />
```

### HTML Structure:
```html
<article class="request-card">
    <a href="{{ route('public.requests.show', ['id' => $request->id, 'slug' => $request->slug]) }}" class="request-card-link">
        <div class="request-card-content">
            <div class="request-card-header">
                <x-badge type="request">طلب</x-badge>
                <x-badge :type="$request->status->value">{{ $request->status->label() }}</x-badge>
            </div>
            <h3 class="request-card-title">{{ $request->title }}</h3>
            <p class="request-card-description">{{ Str::limit($request->description, 120) }}</p>
            @if($showCategory && $request->category)
                <div class="request-card-category">{{ $request->category->name }}</div>
            @endif
            @if($showOffersCount)
                <div class="request-card-offers">عروض: {{ $request->offersCount }}</div>
            @endif
            @if($showDate)
                <div class="request-card-date">{{ $request->createdAt->diffForHumans() }}</div>
            @endif
        </div>
    </a>
</article>
```

---

## 5. Alert Component

### File: `resources/views/components/alert.blade.php`

### Props:
```php
type: 'success' | 'error' | 'warning' | 'info' (required)
dismissible: bool (default: false)
```

### Usage:
```blade
<x-alert type="success" :dismissible="true">
    تم إضافة الإعلان بنجاح
</x-alert>

<x-alert type="error">
    حدث خطأ أثناء المعالجة
</x-alert>
```

### HTML Structure:
```html
<div class="alert alert-{{ $type }}" role="alert">
    @if($dismissible)
        <button class="alert-dismiss" aria-label="إغلاق">×</button>
    @endif
    <div class="alert-content">
        {{ $slot }}
    </div>
</div>
```

---

## 6. Empty State Component

### File: `resources/views/components/empty-state.blade.php`

### Props:
```php
icon: string|null (optional, icon name or emoji)
title: string (required)
description: string|null (optional)
actionLabel: string|null (optional)
actionUrl: string|null (optional)
```

### Usage:
```blade
<x-empty-state 
    icon="📦"
    title="لا توجد إعلانات"
    description="لم يتم العثور على إعلانات مطابقة لبحثك"
    action-label="إضافة إعلان جديد"
    action-url="{{ route('items.create') }}"
/>
```

### HTML Structure:
```html
<div class="empty-state">
    @if($icon)
        <div class="empty-state-icon">{{ $icon }}</div>
    @endif
    <h3 class="empty-state-title">{{ $title }}</h3>
    @if($description)
        <p class="empty-state-description">{{ $description }}</p>
    @endif
    @if($actionLabel && $actionUrl)
        <div class="empty-state-action">
            <x-button type="primary" href="{{ $actionUrl }}">{{ $actionLabel }}</x-button>
        </div>
    @endif
</div>
```

---

## 7. Pagination Component

### File: `resources/views/components/pagination.blade.php`

### Props:
```php
paginator: LengthAwarePaginator (required)
```

### Usage:
```blade
<x-pagination :paginator="$items" />
```

### HTML Structure:
```html
@if($paginator->hasPages())
    <nav class="pagination" aria-label="التنقل بين الصفحات">
        <ul class="pagination-list">
            {{-- Previous Link --}}
            @if($paginator->onFirstPage())
                <li class="pagination-item pagination-item-disabled">
                    <span class="pagination-link">السابق</span>
                </li>
            @else
                <li class="pagination-item">
                    <a href="{{ $paginator->previousPageUrl() }}" class="pagination-link">السابق</a>
                </li>
            @endif

            {{-- Page Numbers --}}
            @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if($page == $paginator->currentPage())
                    <li class="pagination-item pagination-item-active">
                        <span class="pagination-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="pagination-item">
                        <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Link --}}
            @if($paginator->hasMorePages())
                <li class="pagination-item">
                    <a href="{{ $paginator->nextPageUrl() }}" class="pagination-link">التالي</a>
                </li>
            @else
                <li class="pagination-item pagination-item-disabled">
                    <span class="pagination-link">التالي</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
```

**Note:** For large page counts, implement ellipsis logic (show first, last, current ± 2 pages).

---

## 8. Form Input Component

### File: `resources/views/components/form/input.blade.php`

### Props:
```php
name: string (required)
label: string (required)
type: string (default: 'text')
value: string|null (optional)
placeholder: string|null (optional)
required: bool (default: false)
disabled: bool (default: false)
error: string|null (optional, error message)
```

### Usage:
```blade
<x-form.input 
    name="title"
    label="عنوان الإعلان"
    placeholder="أدخل عنوان الإعلان"
    :required="true"
    :error="$errors->first('title')"
/>
```

### HTML Structure:
```html
<div class="form-group">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required)
            <span class="form-required">*</span>
        @endif
    </label>
    <input 
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        class="form-input @error($name) form-input-error @enderror"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
    />
    @error($name)
        <div class="form-error">{{ $message }}</div>
    @elseif($error)
        <div class="form-error">{{ $error }}</div>
    @enderror
</div>
```

---

## 9. Form Textarea Component

### File: `resources/views/components/form/textarea.blade.php`

### Props:
```php
name: string (required)
label: string (required)
value: string|null (optional)
placeholder: string|null (optional)
rows: int (default: 4)
required: bool (default: false)
disabled: bool (default: false)
error: string|null (optional)
```

### Usage:
```blade
<x-form.textarea 
    name="description"
    label="الوصف"
    :rows="6"
    :required="true"
/>
```

---

## 10. Form Select Component

### File: `resources/views/components/form/select.blade.php`

### Props:
```php
name: string (required)
label: string (required)
options: array|Collection (required, ['value' => 'label'])
value: string|null (optional)
placeholder: string|null (optional)
required: bool (default: false)
disabled: bool (default: false)
error: string|null (optional)
```

### Usage:
```blade
<x-form.select 
    name="category_id"
    label="الفئة"
    :options="$categories->pluck('name', 'id')"
    placeholder="اختر الفئة"
    :required="true"
/>
```

### HTML Structure:
```html
<div class="form-group">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required)
            <span class="form-required">*</span>
        @endif
    </label>
    <select 
        id="{{ $name }}"
        name="{{ $name }}"
        class="form-select @error($name) form-select-error @enderror"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
    @error($name)
        <div class="form-error">{{ $message }}</div>
    @elseif($error)
        <div class="form-error">{{ $error }}</div>
    @enderror
</div>
```

---

## 11. Header Component

### File: `resources/views/partials/header.blade.php`

### Structure:
```html
<header class="site-header">
    <div class="container">
        <nav class="navbar">
            <div class="navbar-brand">
                <a href="{{ route('home') }}" class="navbar-logo">
                    <img src="/images/logo.svg" alt="Khezana" />
                </a>
            </div>
            
            <button class="navbar-toggle" aria-label="القائمة" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <div class="navbar-menu">
                <ul class="navbar-nav">
                    <li class="navbar-item">
                        <a href="{{ route('public.items.index') }}" class="navbar-link {{ request()->routeIs('public.items.*') ? 'active' : '' }}">
                            الإعلانات
                        </a>
                    </li>
                    <li class="navbar-item">
                        <a href="{{ route('public.requests.index') }}" class="navbar-link {{ request()->routeIs('public.requests.*') ? 'active' : '' }}">
                            الطلبات
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-auth">
                    @auth
                        <a href="{{ route('dashboard') }}" class="navbar-link">لوحة التحكم</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-ghost">تسجيل الخروج</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost">تسجيل الدخول</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">إنشاء حساب</a>
                    @endauth
                </div>
            </div>
        </nav>
    </div>
</header>
```

---

## 12. Footer Component

### File: `resources/views/partials/footer.blade.php`

### Structure:
```html
<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h4 class="footer-title">عن الموقع</h4>
                <ul class="footer-links">
                    <li><a href="/about">من نحن</a></li>
                    <li><a href="/contact">اتصل بنا</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4 class="footer-title">الفئات</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('public.items.index', ['category_id' => 1]) }}">رجالي</a></li>
                    <li><a href="{{ route('public.items.index', ['category_id' => 2]) }}">نسائي</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4 class="footer-title">المساعدة</h4>
                <ul class="footer-links">
                    <li><a href="/help">كيفية الاستخدام</a></li>
                    <li><a href="/faq">الأسئلة الشائعة</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4 class="footer-title">تابعنا</h4>
                <div class="footer-social">
                    <a href="#" class="social-link" aria-label="فيسبوك">📘</a>
                    <a href="#" class="social-link" aria-label="تويتر">🐦</a>
                    <a href="#" class="social-link" aria-label="إنستغرام">📷</a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p class="footer-copyright">
                © {{ date('Y') }} Khezana. جميع الحقوق محفوظة.
            </p>
        </div>
    </div>
</footer>
```

---

## 13. Container Component

### File: `resources/views/components/container.blade.php`

### Props:
```php
size: 'sm' | 'md' | 'lg' | 'xl' | 'full' (default: 'lg')
```

### Usage:
```blade
<x-container>
    {{-- Content --}}
</x-container>
```

### HTML Structure:
```html
<div class="container container-{{ $size }}">
    {{ $slot }}
</div>
```

### CSS Max-Widths:
- `sm`: 640px
- `md`: 768px
- `lg`: 1024px (default)
- `xl`: 1200px
- `full`: 100%

---

## 14. Section Component

### File: `resources/views/components/section.blade.php`

### Props:
```php
spacing: 'sm' | 'md' | 'lg' | 'xl' (default: 'md')
background: 'white' | 'light' | 'transparent' (default: 'transparent')
```

### Usage:
```blade
<x-section spacing="lg" background="light">
    {{-- Content --}}
</x-section>
```

### HTML Structure:
```html
<section class="section section-spacing-{{ $spacing }} section-bg-{{ $background }}">
    {{ $slot }}
</section>
```

---

## 15. Component Naming Conventions

### File Naming:
- Components: `kebab-case.blade.php`
- Example: `item-card.blade.php`, `form-input.blade.php`

### Class Naming:
- Component classes: `PascalCase`
- Example: `ItemCard`, `FormInput`

### CSS Class Naming:
- BEM-like: `.component-name`, `.component-name-element`, `.component-name--modifier`
- Example: `.item-card`, `.item-card-title`, `.item-card--featured`

### Blade Usage:
- Single word: `<x-button />`
- Multiple words: `<x-items.item-card />` (folder structure)
- Namespaced: `<x-form.input />`

---

## 16. Component Testing Checklist

For each component, verify:

- [ ] Renders correctly in isolation
- [ ] All props work as expected
- [ ] States (hover, active, disabled) work
- [ ] Responsive on mobile/tablet/desktop
- [ ] RTL support (if applicable)
- [ ] Accessibility (keyboard navigation, screen readers)
- [ ] Error handling (missing props, null values)
- [ ] Performance (no layout shift, fast render)

---

**End of Component Specifications**

Use this document as a reference when implementing each Blade component. All components should follow these specifications for consistency.
