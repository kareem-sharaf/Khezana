# Khezana

Laravel 12 project with professional architecture and best practices.

## 🚀 Features

- **Framework**: Laravel 12.47.0
- **PHP**: 8.5.2+
- **Database**: MySQL
- **Authentication**: Laravel Breeze (Blade)
- **Admin Panel**: Filament 5.0
- **Authorization**: Spatie Permission (Roles & Permissions)

## 📦 Installed Packages

### Production
- `filament/filament` - Admin panel
- `spatie/laravel-permission` - Role & permission management
- `laravel/scout` - Full-text search (prepared)
- `spatie/laravel-sitemap` - Sitemap generation
- `spatie/laravel-sluggable` - Slug generation

### Development
- `laravel/breeze` - Authentication scaffolding
- `laravel/telescope` - Debug & monitoring (local only)
- `barryvdh/laravel-debugbar` - Debug toolbar (local only)

## 🏗️ Architecture

This project follows best practices and SOLID principles:

- **Repository Pattern**: `app/Repositories/`
- **Service Layer**: `app/Services/`
- **DTOs**: `app/DTOs/`
- **Actions**: `app/Actions/`
- **Domain-Driven Design**: `app/Domains/`
- **Policies**: `app/Policies/`

See [ARCHITECTURE.md](ARCHITECTURE.md) for detailed information.

## 🔐 Authentication & Authorization

- **Laravel Breeze**: `/login`, `/register`, `/dashboard`
- **Filament Admin**: `/admin`
- **Spatie Permission**: Role-based access control

## 📝 Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## 🔧 Configuration

- Database: `Khezana`
- User: `kareem`
- Password: `kareem`

## 📚 Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Spatie Permission Documentation](https://spatie.be/docs/laravel-permission)

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
