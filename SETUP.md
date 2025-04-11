# Socialite Setup Guide

## Initial Setup

1. Clone the repository
2. Run `composer install`
3. Run `npm install`
4. Copy `.env.example` to `.env` and configure your database
5. Run `php artisan key:generate`
6. Run `php artisan migrate`
7. Link storage directory: `php artisan storage:link`

## Common Issues & Solutions

### Image Storage

Make sure the storage directory is linked correctly:

```bash
php artisan storage:link
```

### Clearing Cache

If you encounter any issues after making changes, try clearing the cache:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Intervention Image

The project uses Intervention Image v3 for image processing. If not installed:

```bash
composer require intervention/image
```

#### Common Code Updates

When using Intervention Image v3, make sure to use the correct syntax:

```php
// Old syntax (v2)
$manager = new ImageManager(Driver::class);
// New syntax (v3)
$manager = new ImageManager(new Driver());

// Old method (v2)
$image->cover(1000, 1000);
// New method (v3)
$image->scale(width: 1000, height: 1000);
```

## Development

Run the development server:

```bash
npm run dev
```

In another terminal:

```bash
php artisan serve
```
