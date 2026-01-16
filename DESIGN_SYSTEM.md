# Khezana Auth Pages - Design System

## Overview

Lightweight, consistent design system for authentication pages optimized for low-bandwidth environments (especially Syria).

## Design Principles

1. **Consistency**: All screens share the same visual spirit
2. **Simplicity**: Clean, calm, and trustworthy look
3. **Performance**: Minimal CSS, no external dependencies
4. **Accessibility**: Large touch targets, clear labels, keyboard accessible
5. **RTL Support**: Full Arabic language support

## Design System

### Colors

**Primary Colors:**
- Primary: `#2563eb` (Blue - trustworthy, professional)
- Primary Hover: `#1d4ed8`
- Primary Light: `#dbeafe`

**Neutral Colors:**
- Text Primary: `#1e293b`
- Text Secondary: `#64748b`
- Text Light: `#94a3b8`
- Border: `#e2e8f0`

**Status Colors:**
- Success: `#10b981`
- Error: `#ef4444`
- Warning: `#f59e0b`

**Backgrounds:**
- Primary: `#ffffff`
- Secondary: `#f8fafc`

### Typography

**Font Stack:**
- English: System fonts (`-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto`)
- Arabic: `'Segoe UI', Tahoma, Arial, sans-serif`

**Font Sizes:**
- Title: `1.75rem` (28px)
- Subtitle: `0.875rem` (14px)
- Body: `1rem` (16px)
- Small: `0.875rem` (14px)
- Hint: `0.75rem` (12px)

### Spacing

Using consistent spacing scale:
- XS: `0.25rem` (4px)
- SM: `0.5rem` (8px)
- MD: `1rem` (16px)
- LG: `1.5rem` (24px)
- XL: `2rem` (32px)
- 2XL: `3rem` (48px)

### Components

#### Form Input
- Border radius: `0.5rem` (8px)
- Padding: `1rem` (16px)
- Border: `1px solid #e2e8f0`
- Focus: Blue border with subtle shadow

#### Buttons
- Height: `44px` minimum (touch-friendly)
- Border radius: `0.5rem` (8px)
- Padding: `1rem 2rem`
- Full width on mobile

#### Cards
- Max width: `440px`
- Border radius: `0.75rem` (12px)
- Shadow: `0 10px 15px -3px rgba(0, 0, 0, 0.1)`
- Padding: `3rem` (48px)

## File Structure

```
resources/views/
├── layouts/
│   └── auth.blade.php          # Auth pages layout
├── components/
│   └── auth/
│       ├── input.blade.php     # Reusable input component
│       ├── button.blade.php    # Reusable button component
│       └── alert.blade.php     # Alert/message component
├── auth/
│   ├── login.blade.php         # Login page
│   └── register.blade.php      # Registration page

public/css/
└── auth.css                    # Lightweight CSS (single file)
```

## Usage

### Login Page

```blade
<x-auth-layout :title="__('Login')" :subtitle="__('Welcome back!')">
    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf
        <x-auth.input name="phone" type="tel" :label="__('Phone Number')" required />
        <x-auth.input name="password" type="password" :label="__('Password')" required />
        <x-auth.button type="submit" variant="primary">{{ __('Login') }}</x-auth.button>
    </form>
</x-auth-layout>
```

### Register Page

```blade
<x-auth-layout :title="__('Create Account')">
    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf
        <x-auth.input name="name" :label="__('Full Name')" required />
        <x-auth.input name="phone" type="tel" :label="__('Phone Number')" required :hint="__('Required for verification')" />
        <x-auth.input name="password" type="password" :label="__('Password')" required />
        <x-auth.button type="submit" variant="primary">{{ __('Register') }}</x-auth.button>
    </form>
</x-auth-layout>
```

## Performance Optimizations

1. **Single CSS File**: All styles in `public/css/auth.css` (~8KB)
2. **No External Fonts**: Uses system fonts only
3. **No JavaScript**: Pure server-side rendering
4. **Minimal HTML**: Clean, semantic markup
5. **No Images**: SVG logo only (inline)

## RTL Support

- Automatic direction detection based on locale
- CSS variables for spacing adapt to RTL
- All flexbox layouts reverse automatically
- Text alignment follows direction

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11+ (with CSS Grid fallback)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Accessibility

- WCAG 2.1 AA compliant
- Keyboard navigation support
- Screen reader friendly
- Touch-friendly targets (44px minimum)
- High contrast ratios
- Clear focus indicators
