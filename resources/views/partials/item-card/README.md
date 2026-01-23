# Item Card Component - Clean UI Component

## 📋 Overview

Item Card is a **clean, dumb UI component** that follows best practices:
- ✅ No logic inside Blade
- ✅ All data passed via props
- ✅ Single Responsibility Principle
- ✅ BEM naming convention
- ✅ Variant support (public/user/compact)
- ✅ RTL support
- ✅ Production-ready

## 🏗️ Architecture

### Component Structure

```
partials/
└── item-card/
    ├── item-card.blade.php    # Main component (dumb)
    └── USAGE.md               # Usage guide
```

### Supporting Files

```
app/
├── View/
│   └── Components/
│       └── ItemCard.php      # Laravel Component class (optional)
└── Helpers/
    └── ItemCardHelper.php    # Data preparation helpers
```

## 🎯 Variants

| Variant | Description | Use Case |
|---------|-------------|----------|
| `public` | Full featured card for public listings | Public item listings |
| `user` | Card for user's own items | My items page |
| `compact` | Minimal card without meta info | Featured items, sidebars |

## 📝 Usage Examples

### Basic Usage (Recommended)

```blade
@php
    use App\Helpers\ItemCardHelper;
@endphp

@include('partials.item-card', array_merge(
    ['item' => $item],
    ItemCardHelper::preparePublicItem($item)
))
```

### With Variant

```blade
@include('partials.item-card', array_merge(
    ['item' => $item],
    ItemCardHelper::prepareUserItem($item, ['variant' => 'user'])
))
```

### Compact Variant

```blade
@include('partials.item-card', array_merge(
    ['item' => $item],
    ItemCardHelper::prepareCompactItem($item, 'public')
))
```

### Manual Props (Full Control)

```blade
@include('partials.item-card', [
    'item' => $item,
    'variant' => 'public',
    'url' => route('public.items.show', ['id' => $item->id]),
    'primaryImage' => $item->primaryImage,
    'images' => $item->images,
    'title' => $item->title,
    'displayPrice' => price_with_fee($item->price, $item->operationType),
    'operationType' => $item->operationType,
    'condition' => $item->condition,
    'category' => $item->category?->name,
    'createdAt' => $item->createdAtFormatted,
    'showMeta' => true,
    'showImagePreview' => true,
])
```

## 🔧 Helper Methods

### `ItemCardHelper::preparePublicItem($item, $overrides = [])`

Prepares data for public items (ItemReadModel).

**Returns:**
```php
[
    'variant' => 'public',
    'url' => route('public.items.show', ...),
    'primaryImage' => $item->primaryImage,
    'images' => $item->images,
    'title' => $item->title,
    'price' => $item->price,
    'displayPrice' => price_with_fee(...),
    'operationType' => $item->operationType,
    'condition' => $item->condition,
    'category' => $item->category?->name,
    'createdAt' => $item->createdAtFormatted,
    'showMeta' => true,
    'showImagePreview' => true,
]
```

### `ItemCardHelper::prepareUserItem($item, $overrides = [])`

Prepares data for user items (Item model).

### `ItemCardHelper::prepareCompactItem($item, $type = 'public', $overrides = [])`

Prepares data for compact variant.

## 🎨 CSS Classes (BEM)

### Base
- `.khezana-item-card` - Base component
- `.khezana-item-card--public` - Public variant
- `.khezana-item-card--user` - User variant
- `.khezana-item-card--compact` - Compact variant

### Elements
- `.khezana-item-card__image` - Image section
- `.khezana-item-card__content` - Content section
- `.khezana-item-card__title` - Title element
- `.khezana-item-card__price` - Price element
- `.khezana-item-card__badge` - Badge element
- `.khezana-item-card__meta` - Meta section

### Modifiers
- `.khezana-item-card__price--free` - Free price
- `.khezana-item-card__badge--sell` - Sell badge
- `.khezana-item-card__badge--rent` - Rent badge
- `.khezana-item-card__badge--donate` - Donate badge

## ✅ Best Practices

1. **Always use Helper methods** - They handle data transformation automatically
2. **Pass overrides when needed** - Customize behavior without modifying helpers
3. **Use appropriate variant** - Choose variant based on context
4. **No logic in component** - Component is dumb, all logic in helpers/controllers
5. **Prepare data in controllers** - When possible, prepare data before passing to view

## 🔄 Migration Guide

### Before (Old Component)

```blade
@include('partials.item-card', ['item' => $item])
```

**Problems:**
- Logic inside Blade (detection of public vs user)
- Hard to customize
- Not reusable

### After (New Component)

```blade
@php
    use App\Helpers\ItemCardHelper;
@endphp

@include('partials.item-card', array_merge(
    ['item' => $item],
    ItemCardHelper::preparePublicItem($item)
))
```

**Benefits:**
- Clean, explicit props
- Easy to customize
- Reusable with variants
- No logic in Blade

## 📊 Props Reference

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `item` | mixed | ✅ | - | Item model or ItemReadModel |
| `variant` | string | ❌ | `'public'` | `'public'` \| `'user'` \| `'compact'` |
| `url` | string | ❌ | auto | Item detail URL |
| `primaryImage` | object\|null | ❌ | auto | Primary image with `path` |
| `images` | Collection | ❌ | `collect()` | Collection of images |
| `title` | string | ❌ | auto | Item title |
| `price` | float\|null | ❌ | auto | Item price |
| `displayPrice` | float\|null | ❌ | auto | Price with fees |
| `operationType` | string | ❌ | auto | `'sell'` \| `'rent'` \| `'donate'` |
| `condition` | string\|null | ❌ | auto | `'new'` \| `'used'` |
| `category` | string\|null | ❌ | auto | Category name |
| `createdAt` | string\|null | ❌ | auto | Formatted date |
| `showMeta` | bool | ❌ | `true` | Show meta info |
| `showImagePreview` | bool | ❌ | `true` | Show hover preview |

## 🚀 Advanced Usage

### Custom Overrides

```blade
@include('partials.item-card', array_merge(
    ['item' => $item],
    ItemCardHelper::preparePublicItem($item, [
        'variant' => 'compact',
        'showMeta' => false,
        'showImagePreview' => false,
    ])
))
```

### In Loops

```blade
@foreach ($items as $item)
    @include('partials.item-card', array_merge(
        ['item' => $item],
        ItemCardHelper::preparePublicItem($item)
    ))
@endforeach
```

## 🎯 Design Principles

1. **Dumb Component** - No business logic
2. **Props-Based** - All data via props
3. **Single Responsibility** - Only renders UI
4. **Reusable** - Works with any data structure
5. **Maintainable** - Easy to modify and extend

## 📚 Related Files

- `app/Helpers/ItemCardHelper.php` - Data preparation
- `app/View/Components/ItemCard.php` - Laravel component (optional)
- `public/css/cards.css` - Component styles
- `resources/views/items/_partials/grid.blade.php` - Usage example

---

**Last Updated**: January 2026  
**Version**: 2.0 (Clean Component)  
**Status**: Production Ready ✅
