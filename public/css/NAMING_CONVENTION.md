# CSS Naming Convention Guide

## 📋 Overview

This document outlines the improved CSS naming convention using BEM modifiers for better clarity and maintainability.

## 🎯 Principles

1. **BEM Methodology**: Block__Element--Modifier
2. **Clear Modifiers**: Use `--modifier` instead of long names
3. **RTL Support**: Use logical properties (`inset-inline-start`, `margin-inline-end`)
4. **Backward Compatibility**: Old classes still work

## 📝 Naming Patterns

### Before (Long Names)
```css
.khezana-item-card-modern
.khezana-items-grid-modern
.khezana-item-image-section
.khezana-item-content-section
.khezana-item-title-wrapper
.khezana-item-secondary-info
```

### After (BEM Modifiers)
```css
.khezana-item-card--modern
.khezana-items-grid--modern
.khezana-item-card__image
.khezana-item-card__content
.khezana-item-card__title
.khezana-item-card__meta
```

## 🏗️ Component Structure

### Item Card Component

**Base:**
- `.khezana-item-card` - Base component

**Modifiers:**
- `.khezana-item-card--modern` - Modern variant (default)
- `.khezana-item-card--public` - Public variant
- `.khezana-item-card--user` - User variant
- `.khezana-item-card--compact` - Compact variant

**Elements:**
- `.khezana-item-card__image` - Image section
- `.khezana-item-card__content` - Content section
- `.khezana-item-card__title` - Title element
- `.khezana-item-card__price` - Price element
- `.khezana-item-card__badge` - Badge element
- `.khezana-item-card__meta` - Meta section

**Element Modifiers:**
- `.khezana-item-card__price--free` - Free price modifier
- `.khezana-item-card__badge--sell` - Sell badge modifier
- `.khezana-item-card__badge--rent` - Rent badge modifier
- `.khezana-item-card__badge--donate` - Donate badge modifier

### Grid Component

**Base:**
- `.khezana-items-grid` - Base grid

**Modifiers:**
- `.khezana-items-grid--modern` - Modern grid variant

## 🌍 RTL Support

All directional properties use logical properties:

### Before (Physical Properties)
```css
margin-right: var(--khezana-spacing-xs);
padding-left: var(--khezana-spacing-md);
right: var(--khezana-spacing-sm);
left: var(--khezana-spacing-sm);
```

### After (Logical Properties)
```css
margin-inline-end: var(--khezana-spacing-xs);
padding-inline-start: var(--khezana-spacing-md);
inset-inline-end: var(--khezana-spacing-sm);
inset-inline-start: var(--khezana-spacing-sm);
```

### RTL Selectors
```css
[dir="rtl"] .khezana-item-card__actions {
    justify-content: flex-start;
}

[dir="rtl"] .khezana-breadcrumb {
    flex-direction: row-reverse;
}
```

## 🔄 Migration Guide

### Old Classes → New Classes

| Old Class | New Class | Notes |
|-----------|-----------|-------|
| `.khezana-item-card-modern` | `.khezana-item-card--modern` | Modifier |
| `.khezana-items-grid-modern` | `.khezana-items-grid--modern` | Modifier |
| `.khezana-item-image-section` | `.khezana-item-card__image` | Element |
| `.khezana-item-content-section` | `.khezana-item-card__content` | Element |
| `.khezana-item-title-wrapper` | `.khezana-item-card__title` | Element |
| `.khezana-item-secondary-info` | `.khezana-item-card__meta` | Element |

### Backward Compatibility

All old classes are still supported. You can:
1. **Keep using old classes** - They still work
2. **Gradually migrate** - Update when convenient
3. **Use new classes** - Recommended for new code

## ✅ Best Practices

1. **Use Modifiers**: `--modern`, `--compact`, `--public`
2. **Use Elements**: `__image`, `__content`, `__title`
3. **Use Logical Properties**: `margin-inline-start`, `inset-inline-end`
4. **Add RTL Support**: Use `[dir="rtl"]` selectors
5. **Document Changes**: Update this guide when adding new patterns

## 📚 Examples

### Item Card Usage

```html
<!-- Modern variant (default) -->
<article class="khezana-item-card khezana-item-card--modern">
    <div class="khezana-item-card__image">...</div>
    <div class="khezana-item-card__content">...</div>
</article>

<!-- Compact variant -->
<article class="khezana-item-card khezana-item-card--compact">
    <div class="khezana-item-card__image">...</div>
    <div class="khezana-item-card__content">...</div>
</article>
```

### Grid Usage

```html
<!-- Modern grid -->
<div class="khezana-items-grid khezana-items-grid--modern">
    <!-- items -->
</div>
```

---

**Last Updated**: January 2026  
**Version**: 2.0  
**Status**: Production Ready ✅
