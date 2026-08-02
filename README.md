# Laravel Hexagonal

[![Package CI](https://github.com/koshuang/laravel-hexagonal/actions/workflows/ci.yml/badge.svg)](https://github.com/koshuang/laravel-hexagonal/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/koshuang/laravel-hexagonal/v/stable)](https://packagist.org/packages/koshuang/laravel-hexagonal)
[![License](https://poser.pugx.org/koshuang/laravel-hexagonal/license)](https://packagist.org/packages/koshuang/laravel-hexagonal)

Laravel scaffolding for modular Hexagonal Architecture (Ports and Adapters).
It gives a fresh Laravel application a repeatable module structure without
shipping an example domain into the application.

The complete Account money-transfer example lives in the companion repository:

<https://github.com/koshuang/laravel-hexagonal-architecture>

## Requirements

- PHP 8.4 or 8.5
- Laravel 13
- `nwidart/laravel-modules` 13

## Installation

```bash
composer require koshuang/laravel-hexagonal
php artisan hexagonal:install
composer update nwidart/laravel-modules deptrac/deptrac
composer dump-autoload
```

The installer is idempotent. Existing files are preserved by default. Use
`--force` only when intentionally replacing generated files:

```bash
php artisan hexagonal:install --force
```

Create a module:

```bash
php artisan hexagonal:make-module Order
```

The generated module has this dependency direction:

```text
Infrastructure -> Application -> Domain
```

- Domain contains business rules and must not depend on Laravel framework classes.
- Application contains use cases and inbound/outbound ports.
- Infrastructure contains Laravel adapters, persistence, routes, and bindings.

## Generated structure

```text
Modules/Order/
├── Application/
│   ├── Port/In
│   ├── Port/Out
│   └── Services
├── Domain/
│   ├── Entities
│   ├── Services
│   └── ValueObjects
├── Infrastructure/
│   ├── Adapter/In
│   ├── Adapter/Out
│   ├── Config
│   └── Providers
└── Tests/
    ├── Feature
    └── Unit
```

The installer also creates `Modules/Shared/Domain/Contracts`, a generic
`deptrac.yaml`, and the `Modules\\` PSR-4 autoload entry in the application.
It adds `deptrac/deptrac` to the application's development dependencies so the
architecture check is reproducible in local development and CI.
It also adds `nwidart/laravel-modules` and enables its Composer merge plugin in
the application root, because Composer plugin permissions are root-project
configuration and cannot be inherited from a package.

Validate the dependency direction after adding module code:

```bash
php artisan hexagonal:validate
```

The generated rules enforce `Infrastructure -> Application -> Domain` and do
not allow Domain code to depend on Laravel framework classes.

## Development

```bash
composer install
composer validate --strict
composer test
composer lint
```

The package development suite includes:

- PHPUnit and Orchestra Testbench for Laravel integration tests
- PHPStan Level 9 with Larastan
- PHPCS with the Onramp Lab Laravel standard
- PHP Insights
- PHPMD
- Deptrac dependency direction checks
- Rector dry-run checks
- GitHub Actions on PHP 8.4 and 8.5

The package keeps its external interface small: the Laravel service provider,
`hexagonal:install`, and `hexagonal:make-module`. File writing and module
scaffolding are internal seams covered by unit tests.

## Versioning

Releases follow Semantic Versioning. The `1.x` line targets Laravel 13 and
PHP 8.4+. Laravel major-version support changes require a compatibility update
in `composer.json`, CI, and this document.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for development and pull request
requirements. Changes are tracked in [CHANGELOG.md](CHANGELOG.md).

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
