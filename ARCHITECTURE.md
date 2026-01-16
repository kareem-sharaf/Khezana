# Khezana - Project Architecture

## 📁 Project Structure

```
app/
├── Actions/          # Action classes (single-purpose operations)
├── DTOs/             # Data Transfer Objects
├── Domains/          # Domain-specific modules (DDD structure)
├── Policies/         # Authorization policies
├── Repositories/     # Repository pattern implementations
│   └── BaseRepository.php
└── Services/         # Service layer (business logic)
    └── BaseService.php

resources/views/
├── components/       # Reusable Blade components
├── layouts/          # Base layouts
└── partials/         # Partial views (header, footer, etc.)

routes/
├── web.php           # Web routes
└── admin.php         # Admin routes (prefixed with /admin)
```

## 🎯 Architecture Patterns

### 1. Repository Pattern
- **BaseRepository**: Abstract base class for all repositories
- Location: `app/Repositories/BaseRepository.php`
- Extend for domain-specific repositories

### 2. Service Layer
- **BaseService**: Abstract base class for business logic
- Location: `app/Services/BaseService.php`
- Handle complex business operations

### 3. DTOs (Data Transfer Objects)
- Location: `app/DTOs/`
- Used for data transformation between layers

### 4. Actions
- Location: `app/Actions/`
- Single-purpose operations (following Single Responsibility Principle)

### 5. Domain-Driven Design (Light DDD)
- Location: `app/Domains/`
- Organize code by business domains

## 🔐 Authentication & Authorization

### Laravel Breeze
- Installed with Blade stack
- Routes: `/login`, `/register`, `/dashboard`

### Spatie Permission
- Installed and configured
- User model has `HasRoles` trait
- Migrations published

### Roles Structure
- **Super Admin**: Full system access
- **Admin**: Administrative access
- **User**: Regular user access

## 🎨 Admin Panel

### Filament
- Panel ID: `admin`
- Path: `/admin`
- Resources: Auto-discovered from `app/Filament/Resources`
- Pages: Auto-discovered from `app/Filament/Pages`
- Widgets: Auto-discovered from `app/Filament/Widgets`

## 📦 Installed Packages

### Production
- `filament/filament` - Admin panel
- `spatie/laravel-permission` - Role & permission management
- `laravel/scout` - Full-text search (prepared, not configured)
- `spatie/laravel-sitemap` - Sitemap generation
- `spatie/laravel-sluggable` - Slug generation

### Development
- `laravel/breeze` - Authentication scaffolding
- `laravel/telescope` - Debug & monitoring (local only)
- `barryvdh/laravel-debugbar` - Debug toolbar (local only)

## 🔧 Configuration

### Environment Variables
- Database: MySQL (Khezana)
- User: kareem / kareem

### Middleware Groups
- `web`: Standard web routes
- `admin`: Admin routes (prefixed with `/admin`)

## 📝 Best Practices Applied

1. **SOLID Principles**: Applied in service and repository layers
2. **Thin Controllers**: Controllers delegate to services
3. **Form Requests**: Use `BaseFormRequest` for validation
4. **Repository Pattern**: Data access abstraction
5. **Service Layer**: Business logic separation
6. **Policies**: Authorization logic separation

## 🚀 Next Steps

1. Configure Scout (search engine) if needed
2. Create domain modules in `app/Domains/`
3. Implement services for business logic
4. Create Form Requests for validation
5. Set up Policies for authorization
6. Configure SEO meta tags structure
