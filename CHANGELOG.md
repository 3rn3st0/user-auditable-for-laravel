# Changelog

All notable changes to `laravel-user-auditable` will be documented in this file.

## [1.1.2] - 2026-02-26

### Fixed
- Fixed CI dependency conflicts for Laravel 12 compatibility.


## [1.1.1] - 2026-02-26

### Added
- Added support for Laravel 12.0.

## [1.1.0] - 2026-02-26

### Added
- Added `Auth::check()` to `restoring` event for security consistency.
- Added `BelongsTo` return types to all relationship methods in `UserAuditable` trait.
- Added `#[Test]` attribute to all tests (PHPUnit 10 conversion).
- Added static cache for `Schema::hasColumn()` checks to improve performance.
- Added parameter type hints to all Blueprint macros.
- Support for custom enum values in `statusColumn()` macro.

### Changed
- Requirement updated to PHP ^8.3 and Laravel ^11.0.
- Improved `deleting` event logic to use direct DB update, avoiding `updated_by` overwrite.
- Relocated test models to `tests/TestModels` (PSR-4 compliance).
- Modernized `ulid` foreign key creation using `foreignUlid()`.
- Improved `tagger.php` with dynamic branch detection and safer command execution.
- Documentation refreshed (README.md) with updated requirements and badges.

### Fixed
- Fixed critical security issue where DB credentials were exposed in `.env.testing`.
- Fixed PSR-4 autoload duplication in `composer.json`.
- Fixed configuration `defaults` being ignored by schema macros.
- Fixed redundant indexes in `uuidColumn` and `ulidColumn`.


## [1.0.2] - 2026-02-02

- Create `tagger.php` for automated Git Tag versioning.
- Fix `release:patch` composer script for use `tagger.php`.
- Update package version from 1.0.1 to 1.0.2
- Update this CHANGELOG.md

## [1.0.1] - 2026-02-02

- Solve `composer require...` issue.
- Update package version from 1.0.0 to 1.0.1

## [1.0.0] - 2025-10-15

### Added
- Initial release! 🎉
- User auditing macros: `userAuditable()`, `fullAuditable()`, etc.
- `UserAuditable` trait for automatic user tracking
- Support for ID, UUID, and ULID key types
- Query scopes: `createdBy()`, `updatedBy()`, `deletedBy()`
- Relationships: `creator()`, `updater()`, `deleter()`
- Comprehensive configuration file
- Service provider with auto-discovery

### Technical
- PSR-4 autoloading
- Laravel package auto-discovery
- Configuration publishing
- MIT License
