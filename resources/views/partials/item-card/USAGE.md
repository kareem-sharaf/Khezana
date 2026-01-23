# Item Card Component - Usage Guide

## 📋 Overview

Item Card is a **clean, dumb UI component** that displays item information. It follows the Single Responsibility Principle and accepts all data via props.

## 🎯 Variants

The component supports three variants:

- **`public`** - For public item listings (default)
- **`user`** - For user's own items
- **`compact`** - Minimal version without meta info

## 📝 Basic Usage

### Method 1: Using Helper (Recommended)

```blade
@php
    use App\Helpers\ItemCardHelper;
@endphp

@include('partials.item-card', array_merge(
    ['item' => $item],
    ItemCardHelper::preparePublicItem($item)
))
```

### Method 2: Using Component Class

```blade
@php
    $itemCard = new \App\View\Components\ItemCard($item, 'public');
@endphp

{!! $itemCard->render() !!}
```

### Method 3: Manual Props (Full Control)

```blade
@include('partials.item-card', [
    'item' => $item,
    'variant' => 'public',
    'url' => route('public.items.show', ['id' => $item->id]),
    'primaryImage' => $item->primaryImage,
    'images' => $item->images,
    'title' => $item->title,
    'price' => $item->price,
    'displayPrice' => price_with_fee($item->price, $item->operationType),
    'operationType' => $item->operationType,
    'condition' => $item->condition,
    'category' => $item->category?->name,
    'createdAt' => $item->createdAtFormatted,
    'showMeta' => true,
    'showImagePreview' => true,
])
```

## 🎨 Variants Examples

### Public Variant

```blade
@include('partials.item-card', array_merge(
    ['item' => $item],
    ItemCardHelper::preparePublicItem($item, ['variant' => 'public'])
))
```

### User Variant

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

## 🔧 Props Reference

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `item` | mixed | ✅ | - | Item model or ItemReadModel |
| `variant` | string | ❌ | `'public'` | `'public'` \| `'user'` \| `'compact'` |
| `url` | string | ❌ | auto | Item detail URL |
| `primaryImage` | object\|null | ❌ | auto | Primary image object with `path` property |
| `images` | Collection | ❌ | `collect()` | Collection of images |
| `title` | string | ❌ | auto | Item title |
| `price` | float\|null | ❌ | auto | Item price |
| `displayPrice` | float\|null | ❌ | auto | Final price with fees |
| `operationType` | string | ❌ | auto | `'sell'` \| `'rent'` \| `'donate'` |
| `condition` | string\|null | ❌ | auto | `'new'` \| `'used'` |
| `category` | string\|null | ❌ | auto | Category name |
| `createdAt` | string\|null | ❌ | auto | Formatted created date |
| `showMeta` | bool | ❌ | `true` | Show secondary meta info |
| `showImagePreview` | bool | ❌ | `true` | Show hover preview |

## 🎯 Helper Methods

### `ItemCardHelper::preparePublicItem($item, $overrides = [])`

Prepares data for public items (ItemReadModel).

```php
$data = ItemCardHelper::preparePublicItem($item, [
    'variant' => 'public',
    'showMeta' => false, // Override default
]);
```

### `ItemCardHelper::prepareUserItem($item, $overrides = [])`

Prepares data for user items (Item model).

```php
$data = ItemCardHelper::prepareUserItem($item, [
    'variant' => 'user',
]);
```

### `ItemCardHelper::prepareCompactItem($item, $type = 'public', $overrides = [])`

Prepares data for compact variant.

```php
$data = ItemCardHelper::prepareCompactItem($item, 'public', [
    'showImagePreview' => false,
]);
```

## 🎨 CSS Classes

The component uses BEM naming convention:

- `.khezana-item-card` - Base component
- `.khezana-item-card--public` - Public variant
- `.khezana-item-card--user` - User variant
- `.khezana-item-card--compact` - Compact variant
- `.khezana-item-card__image` - Image section
- `.khezana-item-card__content` - Content section
- `.khezana-item-card__title` - Title element
- `.khezana-item-card__price` - Price element
- `.khezana-item-card__badge` - Badge element
- `.khezana-item-card__meta` - Meta section

## ✅ Best Practices

1. **Always use Helper methods** for automatic data preparation
2. **Pass overrides** when you need to customize behavior
3. **Use appropriate variant** for the context
4. **Don't add logic** inside the component - it's a dumb component
5. **Prepare data in controllers** or view composers when possible

## 🔍 Examples in Codebase

### In Grid Partial

```blade
{{-- resources/views/items/_partials/grid.blade.php --}}
@php
    use App\Helpers\ItemCardHelper;
@endphp

@foreach ($items as $item)
    @include('partials.item-card', array_merge(
        ['item' => $item],
        ItemCardHelper::prepareUserItem($item)
    ))
@endforeach
```

### With Custom Overrides

```blade
@include('partials.item-card', array_merge(
    ['item' => $item],
    ItemCardHelper::preparePublicItem($item, [
        'showMeta' => false,
        'showImagePreview' => false,
    ])
))
```

## 🚀 Migration from Old Component

**Before:**
```blade
@include('partials.item-card', ['item' => $item])
```

**After:**
```blade
@php
    use App\Helpers\ItemCardHelper;
@endphp

@include('partials.item-card', array_merge(
    ['item' => $item],
    ItemCardHelper::preparePublicItem($item)
))
```

---

**Last Updated**: January 2026  
**Version**: 2.0 (Clean Component)
