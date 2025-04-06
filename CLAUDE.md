# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Build and Development
- **PHP Development**: `php artisan serve` - Run Laravel server
- **Frontend**: `npm run dev` - Run Vite for frontend assets
- **All-in-one**: `composer dev` - Runs server, queue, logs, and Vite

## Tests
- **Run All Tests**: `php artisan test` or `./vendor/bin/phpunit`
- **Run Single Test**: `php artisan test --filter=TestName`
- **Run Test Suite**: `php artisan test --testsuite=Unit`

## Code Style and Conventions
- **PHP Linting**: `./vendor/bin/pint` - Laravel Pint
- **Framework**: Laravel 11 with Livewire 3.5
- **CSS Framework**: Tailwind CSS
- **Naming Conventions**:
  - Models: PascalCase singular (e.g., `Tenant.php`)
  - Controllers: PascalCase with Controller suffix
  - Livewire components: Grouped by feature (Notices, Tenants, etc.)
  - Test methods: snake_case describing functionality
- **File Organization**: Feature-based structure
- **Database**: Migrations, factories, and seeders
- **Error Handling**: Use Laravel validation patterns