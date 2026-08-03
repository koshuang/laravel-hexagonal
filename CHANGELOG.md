# Changelog

All notable changes to this project will be documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and releases follow Semantic Versioning.

## Unreleased

This project is pre-1.0. While it is a well-maintained library, releases
follow `0.x` Semantic Versioning: breaking changes land in `0.x` minor bumps
and do not require a new major until `1.0`. Pin a `^0.<minor>` constraint to
stay stable during onboarding.

### Added

- Use `readonly` in the generated Identity stub for formatter compatibility.
- Publishable package stubs and custom module stub support.
- Initial Laravel 13 package scaffolding.
- Idempotent project installer and module generator.
- PHPUnit, PHPStan, PHPCS, PHP Insights, PHPMD, Rector, and CI baselines.
- Deptrac configuration, dependency installation, and `hexagonal:validate` command.
- Installer bootstrap for Nwidart modules and Composer plugin permissions.
- Corrected generated Deptrac layers for Shared contracts and module Domains.
- Generated Deptrac config covering `./app` and `./Modules` with configurable Carbon and Illuminate allowances.

## [0.1.0] - Initial release

- Initial Laravel 13 package scaffolding, installer, module generator, and
  Deptrac validation baseline.
