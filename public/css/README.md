# Khezana CSS Architecture

## 📋 Overview

This project uses a **Component-based CSS Architecture** with clear separation between components and pages.

## 🏗️ Structure

```
public/css/
├── variables.css          # Design Tokens & CSS Variables
├── base.css               # Base Styles & Reset
├── layout.css             # Layout & Grid Systems
├── header.css             # Header & Navigation
├── buttons.css            # Button Components
├── hero.css               # Hero Section
├── sections.css           # Page Sections
├── forms.css              # Form Components
├── modals.css             # Modal Components
├── footer.css             # Footer Component
├── auth.css               # Auth Pages
├── utilities.css          # Utility Classes
├── components/            # Reusable Components
│   ├── item-card.css     # Item Card Component
│   ├── pagination.css    # Pagination Component
│   └── empty-state.css   # Empty State Component
├── pages/                # Page-Specific Styles
│   ├── items-index.css   # Items Listing Page
│   ├── items-show.css    # Item Detail Page
│   └── requests-index.css # Requests Listing Page
└── home.css              # Main Entry Point
```

## 🎯 Principles

1. **Component-Based**: Reusable components in `components/`
2. **Page-Specific**: Page layouts in `pages/`
3. **Design Tokens**: All variables in `variables.css`
4. **Single Responsibility**: Each file has one clear purpose
5. **Backward Compatibility**: All existing classes maintained

## 📦 Components

### Item Card (`components/item-card.css`)
- `.khezana-item-card` - Base component
- `.khezana-item-card--public` - Public variant
- `.khezana-item-card--user` - User variant
- `.khezana-item-card--compact` - Compact variant
- BEM naming convention
- Grid layout for items

### Pagination (`components/pagination.css`)
- `.khezana-pagination` - Container
- `.khezana-pagination-link` - Page links
- `.khezana-pagination-link.active` - Active state
- `.khezana-pagination-link.disabled` - Disabled state

### Empty State (`components/empty-state.css`)
- `.khezana-empty-state` - Container
- `.khezana-empty-state-icon` - Icon
- `.khezana-empty-state-title` - Title
- `.khezana-empty-state-text` - Description
- `.khezana-empty-actions` - Action buttons

## 📄 Pages

### Items Index (`pages/items-index.css`)
- `.khezana-listing-page` - Page container
- `.khezana-listing-main` - Main content area
- `.khezana-listing-header` - Page header
- `.khezana-listing-sort` - Sort controls
- `.khezana-results-count` - Results counter

### Items Show (`pages/items-show.css`)
- `.khezana-item-detail-page` - Page container
- `.khezana-item-detail-layout` - Grid layout
- `.khezana-item-images` - Image gallery
- `.khezana-item-details` - Content section
- `.khezana-item-header` - Header section
- `.khezana-item-price-section` - Price section
- `.khezana-item-meta` - Meta information
- `.khezana-item-attributes` - Attributes grid
- `.khezana-item-description` - Description section
- `.khezana-item-actions` - Action buttons

### Requests Index (`pages/requests-index.css`)
- `.khezana-requests-grid` - Grid layout
- `.khezana-request-card` - Request card
- `.khezana-request-header` - Card header
- `.khezana-request-content` - Card content
- `.khezana-request-footer` - Card footer
- `.khezana-request-badge` - Status badge
- `.khezana-request-form` - Request form

## 🎨 Design Tokens

All design tokens are defined in `variables.css`:

- **Colors**: `--khezana-primary`, `--khezana-text`, etc.
- **Spacing**: `--khezana-spacing-xs` to `--khezana-spacing-2xl`
- **Typography**: `--khezana-font-size-*`
- **Shadows**: `--khezana-shadow-*`
- **Border Radius**: `--khezana-radius-*`
- **Transitions**: `--khezana-transition`

## 📝 Usage

### Import Order

The import order in `home.css` is critical:

1. Variables (design tokens)
2. Base (reset, typography)
3. Layout (grid, containers)
4. Components (reusable UI)
5. Pages (page-specific)
6. Utilities (helpers)

### Adding New Components

1. Create file in `components/`
2. Use BEM naming: `.khezana-component__element--modifier`
3. Import in `home.css` after other components
4. Document in this README

### Adding New Pages

1. Create file in `pages/`
2. Use page-specific classes: `.khezana-page-name-*`
3. Import in `home.css` after components
4. Document in this README

## ✅ Best Practices

1. **Use Design Tokens**: Always use CSS variables
2. **BEM Naming**: Follow BEM convention for components
3. **Mobile First**: Write mobile styles first
4. **Responsive**: Use media queries for breakpoints
5. **No Duplication**: Reuse components, don't duplicate
6. **Documentation**: Comment complex styles

## 🔄 Migration Notes

- All existing classes are maintained for backward compatibility
- Old files (`cards.css`, `listing.css`, `detail.css`, `requests.css`) are kept but not imported
- New structure is component-based and page-based
- No breaking changes to existing HTML/Blade templates

---

**Last Updated**: January 2026  
**Version**: 2.0  
**Status**: Production Ready ✅
