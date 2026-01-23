# Blade Views Structure - Feature-Based Organization

## 📁 الهيكل الجديد

تم إعادة تنظيم ملفات Blade لتتبع مبدأ **Feature-Based Organization** بدلاً من التنظيم حسب النوع فقط.

## 🏗️ البنية

```
resources/views/
├── items/                          # Feature: Items Management
│   ├── index.blade.php            # View رئيسي خفيف
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── show.blade.php
│   └── _partials/                  # Partials خاصة بـ Items
│       ├── page-header.blade.php
│       ├── grid.blade.php
│       ├── empty-state.blade.php
│       └── pagination.blade.php
│
├── requests/                       # Feature: Requests Management
│   ├── index.blade.php            # View رئيسي خفيف
│   ├── create.blade.php
│   ├── show.blade.php
│   └── _partials/                  # Partials خاصة بـ Requests
│       ├── page-header.blade.php
│       ├── grid.blade.php
│       ├── empty-state.blade.php
│       └── pagination.blade.php
│
├── public/                         # Public-facing features
│   ├── items/
│   │   ├── index.blade.php        # View رئيسي خفيف
│   │   ├── show.blade.php
│   │   └── _partials/
│   │       ├── page-header.blade.php
│   │       ├── grid.blade.php
│   │       ├── empty-state.blade.php
│   │       └── pagination.blade.php
│   └── requests/
│       ├── index.blade.php        # View رئيسي خفيف
│       ├── show.blade.php
│       └── _partials/
│           ├── page-header.blade.php
│           ├── grid.blade.php
│           ├── empty-state.blade.php
│           └── pagination.blade.php
│
├── partials/                       # Shared components (global)
│   ├── header.blade.php
│   ├── footer.blade.php
│   └── item-card.blade.php
│
└── layouts/                        # Base layouts
    ├── app.blade.php
    ├── home.blade.php
    └── auth.blade.php
```

## 📋 مبادئ التصميم

### 1. Single Responsibility Principle
كل partial له مسؤولية واحدة فقط:
- `page-header.blade.php` - رأس الصفحة فقط
- `grid.blade.php` - عرض الشبكة فقط
- `empty-state.blade.php` - حالة فارغة فقط
- `pagination.blade.php` - التصفح فقط

### 2. View الرئيسي خفيف
الـ View الرئيسي يحتوي فقط على:
- `@extends` للـ layout
- `@section('title')`
- `@section('content')` مع includes فقط
- لا منطق معقد

**مثال:**
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

### 3. Partials قابلة لإعادة الاستخدام
كل partial يمكن استخدامه مع معاملات:

```blade
@include('items._partials.empty-state', [
    'type' => 'user',
    'icon' => '📦',
    'title' => __('common.ui.no_items'),
    'message' => __('common.ui.no_items_message'),
    'ctaText' => __('common.ui.no_items_cta'),
    'ctaRoute' => route('items.create'),
])
```

## 🔧 استخدام Partials

### Page Header
```blade
@include('items._partials.page-header', [
    'items' => $items,
    'title' => __('common.ui.my_items_page'), // Optional
    'subtitle' => $items->total() . ' ' . __('items.plural'), // Optional
    'showCreateButton' => true, // Optional, default: true
    'createButtonText' => __('common.ui.add_new_item'), // Optional
    'createButtonRoute' => route('items.create'), // Optional
    'secondaryButton' => [ // Optional
        'text' => __('common.ui.back'),
        'route' => route('home'),
    ],
])
```

### Grid
```blade
@include('items._partials.grid', ['items' => $items])
```

### Empty State
```blade
@include('items._partials.empty-state', [
    'type' => 'user', // 'user' or 'public'
    'icon' => '📦', // Optional
    'title' => __('common.ui.no_items'), // Optional
    'message' => __('common.ui.no_items_message'), // Optional
    'ctaText' => __('common.ui.no_items_cta'), // Optional
    'ctaRoute' => route('items.create'), // Optional
    'ctaClass' => 'khezana-btn-primary khezana-btn-large', // Optional
])
```

### Pagination
```blade
@include('items._partials.pagination', ['items' => $items])
```

## ✅ المزايا

1. **قابلية الصيانة**: كل partial منفصل وسهل التعديل
2. **إعادة الاستخدام**: يمكن استخدام Partials في أماكن متعددة
3. **قابلية التوسع**: إضافة features جديدة سهل
4. **نظافة الكود**: Views رئيسية خفيفة وواضحة
5. **Single Responsibility**: كل partial له مسؤولية واحدة
6. **RTL Support**: جميع Partials تدعم RTL
7. **Best Practices**: يتبع أحدث ممارسات Laravel Blade

## 🚀 إضافة Feature جديد

لإضافة feature جديد:

1. إنشاء مجلد للـ feature:
   ```bash
   mkdir resources/views/my-feature
   mkdir resources/views/my-feature/_partials
   ```

2. إنشاء Partials:
   - `page-header.blade.php`
   - `grid.blade.php`
   - `empty-state.blade.php`
   - `pagination.blade.php`

3. إنشاء View رئيسي:
   ```blade
   @extends('layouts.app')
   
   @section('title', __('my-feature.title'))
   
   @section('content')
       @include('my-feature._partials.page-header')
       @include('my-feature._partials.grid')
       @include('my-feature._partials.pagination')
   @endsection
   ```

## 📝 ملاحظات

- جميع Partials تدعم RTL تلقائياً
- Translations موجودة في `lang/ar/` و `lang/en/`
- لا يتم كسر أي Route أو Translation
- الكود Production-ready

## 🔍 التحقق من الجودة

- ✅ No linter errors
- ✅ All routes working
- ✅ All translations working
- ✅ RTL support maintained
- ✅ Responsive design preserved
- ✅ Accessibility maintained

---

**Last Updated**: January 2026  
**Version**: 2.0 (Feature-Based)
