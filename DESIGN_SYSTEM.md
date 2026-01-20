# Design System - Khezana Marketplace
## Public Website Design Guidelines

**Version:** 1.0.0  
**Target Market:** Syrian Market  
**Platform Type:** Clothing Marketplace (Sell / Rent / Donate / Request)  
**Tech Stack:** Laravel Blade (SSR), Minimal JavaScript

---

## 1. Visual Identity

### 1.1 Color Palette

#### Primary Colors
```
Primary Blue: #2563EB
- Usage: Main CTA buttons, links, active states
- Hex: #2563EB
- RGB: 37, 99, 235
- Contrast: AAA on white background

Primary Dark: #1E40AF
- Usage: Hover states, pressed buttons
- Hex: #1E40AF
- RGB: 30, 64, 175
```

#### Secondary Colors
```
Secondary Teal: #0D9488
- Usage: Secondary actions, badges (Rent)
- Hex: #0D9488
- RGB: 13, 148, 136

Secondary Green: #059669
- Usage: Success states, badges (Donate)
- Hex: #059669
- RGB: 5, 150, 105
```

#### Neutral Colors
```
White: #FFFFFF
- Usage: Backgrounds, cards
- Hex: #FFFFFF

Light Gray: #F9FAFB
- Usage: Section backgrounds, card borders
- Hex: #F9FAFB

Medium Gray: #E5E7EB
- Usage: Dividers, disabled states
- Hex: #E5E7EB

Dark Gray: #6B7280
- Usage: Secondary text, placeholders
- Hex: #6B7280

Black: #111827
- Usage: Primary text, headings
- Hex: #111827
```

#### Semantic Colors
```
Success: #10B981
- Usage: Success messages, completed states
- Hex: #10B981

Warning: #F59E0B
- Usage: Warnings, pending states
- Hex: #F59E0B

Error: #EF4444
- Usage: Errors, rejected states
- Hex: #EF4444

Info: #3B82F6
- Usage: Informational messages
- Hex: #3B82F6
```

#### Operation Type Colors (Badges)
```
Sell: #2563EB (Primary Blue)
Rent: #0D9488 (Secondary Teal)
Donate: #059669 (Secondary Green)
Request: #7C3AED (Purple - #7C3AED)
```

### 1.2 Typography

#### Font Family
**Primary (Arabic):**
- Font: "Cairo" (Google Fonts)
- Weights: 400 (Regular), 600 (Semi-Bold), 700 (Bold)
- Fallback: Arial, sans-serif

**Primary (English):**
- Font: "Inter" (Google Fonts)
- Weights: 400 (Regular), 600 (Semi-Bold), 700 (Bold)
- Fallback: system-ui, -apple-system, sans-serif

**Monospace (Numbers/Code):**
- Font: "Courier New", monospace

#### Type Scale
```
H1 (Page Title):
- Size: 32px (2rem)
- Weight: 700 (Bold)
- Line Height: 1.2
- Margin Bottom: 16px

H2 (Section Title):
- Size: 24px (1.5rem)
- Weight: 700 (Bold)
- Line Height: 1.3
- Margin Bottom: 12px

H3 (Card Title):
- Size: 20px (1.25rem)
- Weight: 600 (Semi-Bold)
- Line Height: 1.4
- Margin Bottom: 8px

H4 (Subsection):
- Size: 18px (1.125rem)
- Weight: 600 (Semi-Bold)
- Line Height: 1.4

Body Large:
- Size: 16px (1rem)
- Weight: 400 (Regular)
- Line Height: 1.6

Body (Default):
- Size: 14px (0.875rem)
- Weight: 400 (Regular)
- Line Height: 1.6

Body Small:
- Size: 12px (0.75rem)
- Weight: 400 (Regular)
- Line Height: 1.5

Caption:
- Size: 11px (0.6875rem)
- Weight: 400 (Regular)
- Line Height: 1.4
```

### 1.3 Spacing System

**Base Unit:** 8px

```
Spacing Scale:
- xs: 4px (0.25rem)
- sm: 8px (0.5rem)
- md: 16px (1rem)
- lg: 24px (1.5rem)
- xl: 32px (2rem)
- 2xl: 48px (3rem)
- 3xl: 64px (4rem)
```

**Usage:**
- Padding/Margin between elements: md (16px)
- Section spacing: xl (32px)
- Card padding: md (16px)
- Button padding: sm (8px) vertical, md (16px) horizontal

---

## 2. UI Components

### 2.1 Buttons

#### Primary Button
**Visual:**
- Background: Primary Blue (#2563EB)
- Text: White (#FFFFFF)
- Border: None
- Border Radius: 6px
- Padding: 8px 16px
- Font: 14px, Semi-Bold (600)

**States:**
- Default: Primary Blue background
- Hover: Primary Dark (#1E40AF) background, cursor pointer
- Active: Slightly darker, scale 0.98
- Disabled: Medium Gray (#E5E7EB) background, Dark Gray (#6B7280) text, cursor not-allowed

**Usage:**
- Main actions (Submit, Create, Save)
- Primary CTAs

**Example:**
```
<button class="btn btn-primary">إضافة إعلان</button>
```

#### Secondary Button
**Visual:**
- Background: Transparent
- Text: Primary Blue (#2563EB)
- Border: 1px solid Primary Blue
- Border Radius: 6px
- Padding: 8px 16px
- Font: 14px, Semi-Bold (600)

**States:**
- Default: Transparent with blue border
- Hover: Light Gray (#F9FAFB) background
- Active: Medium Gray (#E5E7EB) background
- Disabled: Same as Primary disabled

**Usage:**
- Secondary actions (Cancel, Back)
- Alternative options

#### Ghost Button
**Visual:**
- Background: Transparent
- Text: Dark Gray (#6B7280)
- Border: None
- Padding: 8px 16px
- Font: 14px, Regular (400)

**States:**
- Default: Transparent
- Hover: Light Gray (#F9FAFB) background
- Active: Medium Gray (#E5E7EB) background

**Usage:**
- Tertiary actions
- Less important actions

#### Button Sizes
- Small: 6px 12px, 12px font
- Medium (Default): 8px 16px, 14px font
- Large: 12px 24px, 16px font

### 2.2 Badges

#### Operation Type Badges
**Visual:**
- Display: Inline-block
- Padding: 4px 8px
- Border Radius: 4px
- Font: 11px, Semi-Bold (600)
- Text: White

**Colors:**
- Sell: Primary Blue (#2563EB)
- Rent: Secondary Teal (#0D9488)
- Donate: Secondary Green (#059669)
- Request: Purple (#7C3AED)

**Usage:**
- Item cards
- Request cards
- Detail pages

#### Status Badges
**Visual:**
- Same structure as Operation Badges
- Colors:
  - Available/Open: Success Green (#10B981)
  - Pending: Warning Orange (#F59E0B)
  - Closed/Unavailable: Error Red (#EF4444)
  - Approved: Info Blue (#3B82F6)

### 2.3 Cards

#### Item Card
**Structure:**
```
┌─────────────────────────────┐
│ [Image - 16:9 aspect]       │
│                             │
├─────────────────────────────┤
│ [Badge: Sell/Rent/Donate]   │
│ Title (H3)                  │
│ Price: 500 SAR              │
│ Category: Men > Suits       │
│ Location: Damascus          │
│ Posted: 2 days ago          │
└─────────────────────────────┘
```

**Visual:**
- Background: White (#FFFFFF)
- Border: 1px solid Light Gray (#E5E7EB)
- Border Radius: 8px
- Padding: 16px
- Box Shadow: None (flat design)
- Hover: Border color changes to Primary Blue, slight elevation (box-shadow: 0 2px 4px rgba(0,0,0,0.1))

**Image:**
- Aspect Ratio: 16:9
- Object Fit: Cover
- Border Radius: 6px (top corners only)
- Lazy Loading: Yes

**Content:**
- Title: H3, truncated to 2 lines max
- Price: Bold, Primary Blue, 18px
- Category: Body Small, Dark Gray
- Location: Body Small, Dark Gray
- Date: Caption, Medium Gray

**Usage:**
- Items listing page
- Related items section

#### Request Card
**Structure:**
```
┌─────────────────────────────┐
│ [Badge: Request]            │
│ Title (H3)                  │
│ Description (2 lines max)   │
│ Category: Women > Dresses   │
│ Status: Open (Badge)        │
│ Offers: 3                   │
│ Posted: 5 days ago          │
└─────────────────────────────┘
```

**Visual:**
- Same as Item Card
- No image section
- Status badge prominent

### 2.4 Forms

#### Text Input
**Visual:**
- Background: White (#FFFFFF)
- Border: 1px solid Medium Gray (#E5E7EB)
- Border Radius: 6px
- Padding: 10px 12px
- Font: 14px, Regular
- Width: 100%

**States:**
- Default: Medium Gray border
- Focus: Primary Blue border, outline: 2px solid Primary Blue (offset 2px)
- Error: Error Red border (#EF4444)
- Disabled: Light Gray background, Medium Gray border, cursor not-allowed

**Label:**
- Position: Above input
- Font: 12px, Semi-Bold
- Color: Black (#111827)
- Margin Bottom: 4px

**Placeholder:**
- Color: Dark Gray (#6B7280)
- Font: 14px, Regular

**Error Message:**
- Position: Below input
- Font: 12px, Regular
- Color: Error Red (#EF4444)
- Margin Top: 4px

#### Textarea
**Visual:**
- Same as Text Input
- Min Height: 100px
- Resize: Vertical only

#### Select Dropdown
**Visual:**
- Same as Text Input
- Background: White with dropdown arrow (CSS arrow)
- Padding Right: 32px (for arrow)

**States:**
- Same as Text Input
- Hover: Light Gray background on options

#### Checkbox
**Visual:**
- Size: 18px × 18px
- Border: 2px solid Medium Gray
- Border Radius: 4px
- Background: White when unchecked
- Background: Primary Blue when checked
- Checkmark: White, centered

**States:**
- Default: Unchecked
- Checked: Primary Blue background
- Hover: Border color Primary Blue
- Disabled: Light Gray background, cursor not-allowed

#### Radio Button
**Visual:**
- Size: 18px × 18px
- Border: 2px solid Medium Gray
- Border Radius: 50% (circle)
- Background: White when unchecked
- Inner dot: 8px, Primary Blue when checked

**States:**
- Same as Checkbox

### 2.5 Alerts

#### Success Alert
**Visual:**
- Background: Light Green (#D1FAE5)
- Border: 1px solid Success Green (#10B981)
- Border Left: 4px solid Success Green
- Border Radius: 6px
- Padding: 12px 16px
- Icon: Checkmark (optional, left side)
- Text: Success Green (#059669)

#### Error Alert
**Visual:**
- Background: Light Red (#FEE2E2)
- Border: 1px solid Error Red (#EF4444)
- Border Left: 4px solid Error Red
- Border Radius: 6px
- Padding: 12px 16px
- Icon: X mark (optional, left side)
- Text: Error Red (#EF4444)

#### Warning Alert
**Visual:**
- Background: Light Orange (#FEF3C7)
- Border: 1px solid Warning Orange (#F59E0B)
- Border Left: 4px solid Warning Orange
- Border Radius: 6px
- Padding: 12px 16px
- Icon: Exclamation (optional, left side)
- Text: Warning Orange (#D97706)

#### Info Alert
**Visual:**
- Background: Light Blue (#DBEAFE)
- Border: 1px solid Info Blue (#3B82F6)
- Border Left: 4px solid Info Blue
- Border Radius: 6px
- Padding: 12px 16px
- Icon: Info (optional, left side)
- Text: Info Blue (#1E40AF)

### 2.6 Empty States

#### Empty List State
**Visual:**
- Icon: Large, centered (64px × 64px), Medium Gray (#6B7280)
- Title: H3, centered, Dark Gray (#6B7280)
- Description: Body, centered, Medium Gray, max-width 400px
- CTA Button: Primary Button, centered (optional)

**Usage:**
- No items found
- No requests found
- Empty search results

**Example:**
```
Icon: 📦 (or SVG)
Title: "لا توجد إعلانات"
Description: "لم يتم العثور على إعلانات مطابقة لبحثك"
CTA: "إضافة إعلان جديد" (redirects to login if not authenticated)
```

### 2.7 Pagination

#### Visual Design
**Structure:**
```
[Previous] [1] [2] [3] ... [10] [Next]
```

**Visual:**
- Container: Flexbox, centered, gap: 4px
- Page Number: 
  - Size: 36px × 36px
  - Border: 1px solid Medium Gray
  - Border Radius: 6px
  - Background: White
  - Text: 14px, centered
  - Hover: Primary Blue border
- Active Page:
  - Background: Primary Blue
  - Text: White
  - Border: None
- Disabled (Previous/Next):
  - Background: Light Gray
  - Text: Medium Gray
  - Cursor: not-allowed
- Ellipsis (...):
  - No border, no background
  - Text: Dark Gray

**Usage:**
- Items listing
- Requests listing
- Server-side pagination (no JS)

### 2.8 Navbar

#### Structure
```
┌─────────────────────────────────────────────────────────┐
│ [Logo]  [Items] [Requests] [Categories]  [Login] [Signup]│
└─────────────────────────────────────────────────────────┘
```

**Visual:**
- Background: White (#FFFFFF)
- Border Bottom: 1px solid Light Gray (#E5E7EB)
- Height: 64px
- Padding: 0 24px
- Position: Sticky top (when scrolling)

**Logo:**
- Size: 120px width (or appropriate)
- Left side
- Click: Home page

**Navigation Links:**
- Font: 14px, Semi-Bold
- Color: Dark Gray (#6B7280)
- Padding: 8px 16px
- Hover: Primary Blue color
- Active: Primary Blue color, underline (2px solid)

**Auth Buttons:**
- Login: Ghost Button
- Signup: Primary Button
- Right side

**Mobile:**
- Hamburger menu (3 lines icon)
- Collapsible menu
- Full width on mobile

### 2.9 Footer

#### Structure
```
┌─────────────────────────────────────────────────────────┐
│ [About] [Categories] [Help] [Contact]                   │
│                                                         │
│ [Social Icons]                                          │
│                                                         │
│ Copyright © 2024 Khezana. All rights reserved.         │
└─────────────────────────────────────────────────────────┘
```

**Visual:**
- Background: Light Gray (#F9FAFB)
- Border Top: 1px solid Medium Gray (#E5E7EB)
- Padding: 48px 24px 24px
- Text: Body Small, Dark Gray

**Links:**
- Display: Grid (4 columns on desktop, 2 on tablet, 1 on mobile)
- Color: Dark Gray
- Hover: Primary Blue

**Social Icons:**
- Size: 24px × 24px
- Spacing: 16px between icons
- Color: Dark Gray
- Hover: Primary Blue

---

## 3. Layout System

### 3.1 Main Layout Structure

```
┌─────────────────────────────────────┐
│           Header (Navbar)           │
├─────────────────────────────────────┤
│                                     │
│           Main Content              │
│         (Container: 1200px)        │
│                                     │
├─────────────────────────────────────┤
│              Footer                 │
└─────────────────────────────────────┘
```

### 3.2 Grid System

**Base:** 12-column grid

**Breakpoints:**
```
Mobile: < 640px
- Columns: 1
- Padding: 16px

Tablet: 640px - 1024px
- Columns: 2 (items per row)
- Padding: 24px

Desktop: > 1024px
- Columns: 3-4 (items per row)
- Max Width: 1200px
- Padding: 24px
```

**Container:**
- Max Width: 1200px
- Margin: 0 auto
- Padding: 0 24px (responsive)

**Grid Gaps:**
- Mobile: 16px
- Tablet: 20px
- Desktop: 24px

### 3.3 Blade Layout Structure

**Base Layout (`layouts/app.blade.php`):**
```
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <!-- Meta, CSS, Fonts -->
</head>
<body>
    @include('partials.header')
    
    <main class="main-content">
        @yield('content')
    </main>
    
    @include('partials.footer')
</body>
</html>
```

**Content Sections:**
- `.container`: Max-width container
- `.section`: Vertical spacing (32px)
- `.grid`: Grid container
- `.card-grid`: Items/Requests grid

---

## 4. UX Principles

### 4.1 Guest User Experience

**Core Principle:** Zero friction browsing, clear authentication prompts

**Allowed Actions (No Login Required):**
- ✅ Browse all items (approved, available)
- ✅ Browse all requests (approved, open)
- ✅ View item details (full information)
- ✅ View request details (full information)
- ✅ Filter by category, price, operation type
- ✅ Search items/requests
- ✅ View user profiles (public info only)

**Restricted Actions (Require Login):**
- ❌ Create item/request
- ❌ Edit own items/requests
- ❌ Submit offer
- ❌ Contact seller/requester
- ❌ Save favorites (if implemented)

**Authentication Flow:**
1. User clicks restricted action
2. Redirect to login page
3. Show alert message: "يجب تسجيل الدخول لإتمام هذه العملية"
4. After login, redirect back to intended action

**No Confusion Rules:**
- All buttons/links that require auth must be clearly labeled
- Disabled state for guest users (visual + tooltip)
- Consistent messaging across all restricted actions

### 4.2 Navigation & Wayfinding

**Breadcrumbs:**
- Show on detail pages
- Format: Home > Category > Item Title
- Clickable links

**Clear CTAs:**
- Primary actions: Prominent, clear labels
- Secondary actions: Less prominent
- No hidden actions

**Search & Filters:**
- Always visible (sticky on scroll)
- Clear filter labels
- Show active filters
- Easy reset

### 4.3 Performance & Slow Internet

**Image Strategy:**
- Thumbnails: Max 400px width, WebP format
- Lazy loading: All images below fold
- Placeholder: Light Gray background, loading spinner
- Alt text: Always provided

**Content Loading:**
- Progressive enhancement
- Critical CSS inline
- Non-critical CSS deferred
- No render-blocking JS

**Progressive Disclosure:**
- Show essential info first
- Expandable sections for details
- Pagination (not infinite scroll)

### 4.4 Accessibility

**Keyboard Navigation:**
- All interactive elements focusable
- Visible focus indicators (2px Primary Blue outline)
- Logical tab order

**Screen Readers:**
- Semantic HTML (header, main, nav, footer)
- ARIA labels where needed
- Alt text for images
- Form labels associated with inputs

**Color Contrast:**
- All text: WCAG AA minimum
- Important text: WCAG AAA
- No color-only information

### 4.5 Error Handling

**404 Page:**
- Clear message: "الصفحة غير موجودة"
- Search box
- Link to home
- Link to browse items

**500 Error:**
- Generic message: "حدث خطأ. يرجى المحاولة لاحقاً"
- No technical details
- Contact link

**Form Errors:**
- Inline validation
- Clear error messages
- Highlighted error fields
- Summary at top of form

---

## 5. Performance Rules

### 5.1 CSS Strategy

**Single Stylesheet:**
- One main CSS file: `public/css/app.css`
- Minified in production
- No inline styles (except critical CSS)

**CSS Structure:**
```
1. Reset/Normalize
2. Base styles (typography, colors)
3. Layout (grid, containers)
4. Components (buttons, cards, forms)
5. Utilities (spacing, text utilities)
```

**No CSS Frameworks:**
- Custom CSS only
- No Tailwind, Bootstrap, etc.
- Lightweight, purpose-built

### 5.2 JavaScript Strategy

**Minimal JS:**
- Only when absolutely necessary
- Vanilla JavaScript (no frameworks)
- Examples:
  - Image lazy loading
  - Mobile menu toggle
  - Form validation (client-side, server-side is primary)

**No JS Dependencies:**
- No jQuery
- No React/Vue
- No heavy libraries

### 5.3 Image Optimization

**Formats:**
- Primary: WebP (with JPEG fallback)
- Max width: 1200px (full width)
- Thumbnails: 400px width

**Lazy Loading:**
- Native `loading="lazy"` attribute
- Intersection Observer for older browsers

**Responsive Images:**
- `srcset` for different screen sizes
- `sizes` attribute

### 5.4 Font Loading

**Strategy:**
- Google Fonts with `display=swap`
- Preconnect to fonts.gstatic.com
- Subset fonts (Arabic + Latin only)

**Fallbacks:**
- System fonts as fallback
- No FOIT (Flash of Invisible Text)

### 5.5 Caching Strategy

**Browser Caching:**
- CSS/JS: 1 year
- Images: 1 month
- HTML: No cache (always fresh)

**CDN (if available):**
- Static assets on CDN
- Images on CDN

---

## 6. Component Implementation Guide

### 6.1 Blade Component Structure

**Location:** `resources/views/components/`

**Naming:**
- Kebab-case: `item-card.blade.php`
- PascalCase class: `ItemCard`

**Usage:**
```blade
<x-item-card :item="$item" />
```

**Props:**
- Pass data via attributes
- Type-hint in component class (optional)

### 6.2 Component List

**Core Components:**
1. `button` - Reusable button component
2. `badge` - Operation/Status badges
3. `item-card` - Item display card
4. `request-card` - Request display card
5. `alert` - Alert messages
6. `empty-state` - Empty list states
7. `pagination` - Pagination links
8. `form-input` - Text input with label
9. `form-textarea` - Textarea with label
10. `form-select` - Select dropdown with label

**Layout Components:**
1. `header` - Site header/navbar
2. `footer` - Site footer
3. `container` - Max-width container
4. `section` - Content section with spacing

### 6.3 Component Props Examples

**Button Component:**
```blade
<x-button type="primary" size="medium" :disabled="false">
    إضافة إعلان
</x-button>
```

**Badge Component:**
```blade
<x-badge type="sell">بيع</x-badge>
<x-badge type="rent">تأجير</x-badge>
```

**Item Card Component:**
```blade
<x-item-card 
    :item="$item" 
    :show-category="true"
    :show-location="true"
/>
```

**Alert Component:**
```blade
<x-alert type="success" :dismissible="true">
    تم إضافة الإعلان بنجاح
</x-alert>
```

---

## 7. Responsive Breakpoints

### 7.1 Mobile First Approach

**Base Styles:** Mobile (< 640px)

**Media Queries:**
```css
/* Tablet */
@media (min-width: 640px) { }

/* Desktop */
@media (min-width: 1024px) { }

/* Large Desktop */
@media (min-width: 1280px) { }
```

### 7.2 Layout Changes by Breakpoint

**Items Grid:**
- Mobile: 1 column
- Tablet: 2 columns
- Desktop: 3 columns
- Large Desktop: 4 columns

**Navbar:**
- Mobile: Hamburger menu
- Tablet+: Full navigation

**Container Padding:**
- Mobile: 16px
- Tablet+: 24px

---

## 8. RTL Support

### 8.1 Arabic Layout

**Direction:**
- `dir="rtl"` on `<html>` tag
- CSS: Use logical properties (margin-inline-start, etc.)

**Text Alignment:**
- Arabic: Right-aligned
- English: Left-aligned
- Mixed: Auto (browser handles)

**Icons:**
- Flip horizontal icons (arrows, chevrons)
- Keep directional icons (maps, etc.)

### 8.2 Font Loading

**Arabic Font:**
- Cairo (Google Fonts)
- Supports Arabic + Latin

**Font Weight:**
- Regular (400) for body
- Semi-Bold (600) for headings
- Bold (700) for emphasis

---

## 9. Implementation Checklist

### Phase 1: Foundation
- [ ] Set up base CSS file
- [ ] Implement color variables
- [ ] Implement typography system
- [ ] Implement spacing system
- [ ] Set up grid system

### Phase 2: Core Components
- [ ] Button component
- [ ] Badge component
- [ ] Form components (input, textarea, select)
- [ ] Alert component
- [ ] Empty state component

### Phase 3: Layout Components
- [ ] Header/Navbar
- [ ] Footer
- [ ] Container
- [ ] Section wrapper

### Phase 4: Content Components
- [ ] Item card
- [ ] Request card
- [ ] Pagination
- [ ] Breadcrumbs

### Phase 5: Pages
- [ ] Home page
- [ ] Items listing
- [ ] Item detail
- [ ] Requests listing
- [ ] Request detail
- [ ] 404 page
- [ ] 500 page

### Phase 6: Polish
- [ ] RTL support
- [ ] Responsive testing
- [ ] Performance optimization
- [ ] Accessibility audit
- [ ] Browser testing

---

## 10. Design Tokens Reference

### Colors (Quick Reference)
```css
--color-primary: #2563EB;
--color-primary-dark: #1E40AF;
--color-secondary-teal: #0D9488;
--color-secondary-green: #059669;
--color-success: #10B981;
--color-warning: #F59E0B;
--color-error: #EF4444;
--color-info: #3B82F6;
--color-white: #FFFFFF;
--color-light-gray: #F9FAFB;
--color-medium-gray: #E5E7EB;
--color-dark-gray: #6B7280;
--color-black: #111827;
```

### Spacing (Quick Reference)
```css
--spacing-xs: 4px;
--spacing-sm: 8px;
--spacing-md: 16px;
--spacing-lg: 24px;
--spacing-xl: 32px;
--spacing-2xl: 48px;
--spacing-3xl: 64px;
```

### Typography (Quick Reference)
```css
--font-family-arabic: 'Cairo', Arial, sans-serif;
--font-family-english: 'Inter', system-ui, sans-serif;
--font-size-h1: 32px;
--font-size-h2: 24px;
--font-size-h3: 20px;
--font-size-body: 14px;
--font-size-small: 12px;
--font-size-caption: 11px;
```

---

**End of Design System Document**

This document serves as the complete guide for implementing the public website UI. All components, styles, and UX patterns should follow these specifications.
