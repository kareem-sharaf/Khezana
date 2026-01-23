# ViewModels Pattern - Documentation

## 📋 Overview

This project uses the **ViewModel/Presenter Pattern** to remove all business logic and calculations from Blade templates. All data preparation happens in ViewModels, making Blade templates clean, readable, and testable.

## 🏗️ Architecture

### Structure

```
app/ViewModels/
├── Items/
│   ├── ItemCardViewModel.php      # Item card display data
│   └── ItemDetailViewModel.php    # Item detail page data
└── Requests/
    └── RequestCardViewModel.php   # Request card display data
```

## 🎯 Principles

1. **No Logic in Blade** - All calculations, conditionals, and formatting in ViewModels
2. **Single Responsibility** - Each ViewModel handles one view's data
3. **Testable** - ViewModels can be unit tested independently
4. **Reusable** - ViewModels can be used in multiple contexts
5. **Type-Safe** - Readonly properties ensure immutability

## 📝 ViewModels

### ItemCardViewModel

**Purpose**: Prepares data for item card component

**Properties**:
- `itemId`, `variant`, `url`, `title`
- `primaryImagePath`, `images`, `hasMultipleImages`
- `price`, `displayPrice`, `formattedPrice`, `formattedDisplayPrice`
- `operationType`, `operationTypeLabel`, `operationTypeBadgeClass`
- `condition`, `conditionLabel`
- `category`, `createdAt`
- `showMeta`, `showImagePreview`
- `isFree`, `isRent`, `showPriceUnit`
- `previewImages`

**Usage**:
```php
$viewModel = ItemCardViewModel::fromArray([
    'itemId' => $item->id,
    'variant' => 'public',
    'url' => route('public.items.show', ['id' => $item->id]),
    // ... other data
]);

// In Blade
@include('partials.item-card', $viewModel->toArray())
```

### ItemDetailViewModel

**Purpose**: Prepares data for item detail pages

**Properties**:
- All item information
- Formatted prices, dates
- Image URLs array
- Approval status information
- Permission flags (canEdit, canDelete, etc.)
- Breadcrumbs array
- Action URLs

**Usage**:
```php
$viewModel = ItemDetailViewModel::fromItem($item, 'user');

// In Controller
return view('items.show', ['viewModel' => $viewModel]);

// In Blade
@include('items._partials.detail.header', ['viewModel' => $viewModel])
```

### RequestCardViewModel

**Purpose**: Prepares data for request card component

**Properties**:
- `requestId`, `variant`, `url`, `title`
- `category`, `description`, `descriptionPreview`
- `status`, `statusLabel`, `statusBadgeClass`
- `approvalStatus`, `approvalStatusLabel`, `approvalStatusClass`
- `attributes`, `displayAttributes`
- `createdAtFormatted`, `userName`
- `offersCount`, `offersText`

**Usage**:
```php
$viewModel = RequestCardViewModel::fromRequest($request, 'public');

// In Blade
@include('requests._partials.grid', ['viewModel' => $viewModel])
```

## 🔧 Implementation

### Creating a ViewModel

```php
<?php

namespace App\ViewModels\Items;

class MyViewModel
{
    public function __construct(
        public readonly string $title,
        public readonly ?float $price,
        public readonly string $formattedPrice,
        // ... other properties
    ) {}

    public static function fromItem($item): self
    {
        return new self(
            title: $item->title ?? '',
            price: $item->price ?? null,
            formattedPrice: $item->price ? number_format((float) $item->price, 0) : '',
            // ... other calculations
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
```

### Using in Controllers

```php
public function show(Item $item): View
{
    $item->load(['category', 'images']);
    
    $viewModel = ItemDetailViewModel::fromItem($item, 'user');
    
    return view('items.show', ['viewModel' => $viewModel]);
}
```

### Using in Blade

```blade
{{-- Clean, no logic --}}
<h1>{{ $viewModel->title }}</h1>
@if ($viewModel->hasPrice)
    <div class="price">{{ $viewModel->formattedDisplayPrice }}</div>
@endif
```

## ✅ Benefits

1. **Separation of Concerns** - Logic separated from presentation
2. **Testability** - ViewModels can be unit tested
3. **Reusability** - Same ViewModel for different contexts
4. **Maintainability** - Changes in one place
5. **Type Safety** - Readonly properties prevent mutations
6. **Performance** - Calculations done once, not in loops

## 🎨 Best Practices

1. **Use readonly properties** - Prevents accidental mutations
2. **Calculate once** - Format prices, dates in constructor
3. **Provide helper methods** - For computed values
4. **Use Collections** - For array operations
5. **Keep it simple** - ViewModels should be data containers

## 📊 Comparison

### Before (Logic in Blade)

```blade
@php
    $displayPrice = price_with_fee((float) $item->price, $item->operation_type->value);
    $isFree = $item->operation_type->value === 'donate';
    $formattedPrice = $displayPrice ? number_format($displayPrice, 0) : '';
@endphp

@if ($displayPrice !== null)
    <div>{{ $formattedPrice }} {{ __('common.ui.currency') }}</div>
@elseif ($isFree)
    <div>{{ __('common.ui.free') }}</div>
@endif
```

### After (ViewModel)

```blade
@if ($viewModel->hasPrice)
    <div>{{ $viewModel->formattedDisplayPrice }} {{ __('common.ui.currency') }}</div>
@elseif ($viewModel->isFree)
    <div>{{ __('common.ui.free') }}</div>
@endif
```

## 🚀 Migration Guide

1. **Identify logic in Blade** - Find `@php` blocks, calculations
2. **Create ViewModel** - Move logic to ViewModel
3. **Update Controller** - Pass ViewModel to view
4. **Update Blade** - Use ViewModel properties
5. **Test** - Verify functionality

---

**Last Updated**: January 2026  
**Version**: 1.0  
**Status**: Production Ready ✅
