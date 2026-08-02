# Laravel Hexagonal

Laravel scaffolding for modular Hexagonal Architecture (Ports and Adapters).

This package is the reusable installation layer. The Account money-transfer application remains in the separate demo repository:

<https://github.com/koshuang/laravel-hexagonal-architecture>

## Requirements

- PHP 8.4+
- Laravel 13
- `nwidart/laravel-modules` 13

## Install

```bash
composer require koshuang/laravel-hexagonal
php artisan hexagonal:install
composer dump-autoload
```

The installer creates the `Modules/Shared` domain contracts, a generic `deptrac.yaml`, and the `Modules\\` PSR-4 autoload entry. Existing files are preserved unless `--force` is supplied.

Create a module:

```bash
php artisan hexagonal:make-module Order
```

Each module has three explicit layers:

```text
Infrastructure -> Application -> Domain
```

Domain code must not depend on Laravel framework classes. Application code owns inbound and outbound ports. Infrastructure contains Laravel adapters, persistence, routes, and dependency injection bindings.

## Current scope

The first package version provides the Laravel service provider, project installer, module generator, shared domain contracts, and a generic Deptrac configuration. Quality-tool presets, architecture test generation, and frontend scaffolding will be added after the installer is validated against a clean Laravel application.

## Local development

The package repository is intentionally separate from the complete demo application. During development, the demo can consume this package through a Composer path repository:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../laravel-hexagonal"
        }
    ]
}
```
