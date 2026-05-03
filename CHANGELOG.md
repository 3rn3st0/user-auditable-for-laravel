# Changelog

All notable changes to `laravel-user-auditable` will be documented in this file.

<!-- markdownlint-disable MD024 -->

## [1.5.0] - 2026-05-03

### Added

- `ChangeAuditable` trait: logs model changes (create, update, delete, restore, revert) to a dedicated audit log table.
  - `auditLogs(): MorphMany` — polymorphic relationship to all audit log entries for the model.
  - `lastAuditLog(): ?AuditLog` — retrieves the most recent audit entry.
  - `revertTo(AuditLog $log): bool` — restores the model to a previous state using `old_values` from the given log; creates a `reverted` entry afterwards.
  - `diffBetween(AuditLog $from, AuditLog $to): array` — returns a field-by-field diff between two audit log entries.
  - Respects `$hidden` and `$auditExclude` (denylist) or `$auditInclude` (allowlist) model properties.
  - Supports custom user resolvers via config (`user_resolver`, `user_type_resolver`).
  - Captures `ip_address` and `user_agent` from the current request automatically.
  - Compatible with `UserAuditable` and `EventAuditable` traits simultaneously.
- `AuditLog` Eloquent model (`ErnestoCh\UserAuditable\Models\AuditLog`):
  - Polymorphic `auditable()` relationship.
  - `user(): BelongsTo` relationship resolved from `user_type`.
  - `pruneOlderThan(int $days): int` static method to delete old entries.
  - Table name driven by `config('user-auditable.change_tracking.table')`.
- `auditLogTable()` Blueprint macro: creates the standard audit log table with all required columns and indexes.
- `dropAuditLogTable()` Blueprint macro: drops the audit log table.
- `change_tracking` configuration section in `config/user-auditable.php`:
  - `enabled`, `table`, `retain_days`, `log_created`, `log_updated`, `log_deleted`, `log_restored`
  - `user_resolver`, `user_type_resolver` callables for custom auth resolution.
- Full test suite for `ChangeAuditable` (10 tests), `AuditLog` model (1 test), and audit log macros (2 tests).

### Changed

- `EventAuditable`: `$auditableColumnCache` visibility unified to `protected` to resolve PHP fatal error when combined with `UserAuditable` in the same model.
- Configuration (`config/user-auditable.php`): added `audit_log_table` and `drop_audit_log_table` to `enabled_macros`.

## [1.4.0] - 2026-05-02

### Added

- `dropFullAuditable(bool $dropForeign = true)` macro: reverses `fullAuditable()` in `down()` migrations.
- `dropUuidColumn(string $columnName = 'uuid')` macro: removes UUID columns created with `uuidColumn()`.
- `dropUlidColumn(string $columnName = 'ulid')` macro: removes ULID columns created with `ulidColumn()`.
- `dropStatusColumn(string $columnName = 'status')` macro: removes status enum columns created with `statusColumn()`.
- `EventAuditable` trait: provides dynamic access to custom event relationships and query scopes:
  - Instance methods: `$model->releasedBy()`, `$model->approvedAt()` (dynamically handle any `{event}_by` or `{event}_at` columns)
  - Static scopes: `Model::releasedBy($userId)->get()` (dynamically filter by any event user)
  - Automatic column detection with caching for performance
- Comprehensive test suite for `EventAuditable` trait (12 tests)
- Comprehensive test suite for all drop macros (11 tests)
- Test fixture `TestModelWithEventAuditable` for event auditable testing

### Changed

- Configuration file (`config/user-auditable.php`): added new drop macros to `enabled_macros` list
- Documentation (README.md):
  - Added `EventAuditable` to features list
  - Added new "Reversing Migrations" section with examples for all drop macros
  - Reorganized Models section to include `EventAuditable` trait example
  - Reorganized Relationships section with subsections for `UserAuditable` and `EventAuditable`
  - Reorganized Query Scopes section with subsections for `UserAuditable` and `EventAuditable`
  - Updated Available Macros table with all new drop macros

## [1.3.0] - 2026-02-28

### Added

- `eventAuditable(string $event, ?string $column = null)` macro: creates `{event}_at`
  (timestamp) and/or `{event}_by` (FK to users) for any custom audit event.
- `dropEventAuditable(string $event, ?string $column = null, bool $dropForeign = true)`
  macro: reverses `eventAuditable` in `down()` migrations.
- Tests for both new macros (column creation, individual specifiers, validation exceptions,
  and drop on non-SQLite).

## [1.2.1] - 2026-02-28

### Changed

- CI jobs for Laravel `9.*` are now marked `continue-on-error: true` due to
  Packagist security advisories blocking all Laravel 9.x versions (EOL Feb 2024).
- Added Laravel 9 EOL notice to README.

## [1.2.0] - 2026-02-28

### Added

- Support for PHP 8.1 and 8.2 (constraint widened from `^8.3` to `^8.1`).
- Support for Laravel 9.x and 10.x (constraint widened from `^11.0` to `^9.0|^10.0|^11.0|^12.0`).
- `RuntimeException` guard in `fullAuditable()` when `user_auditable` is not listed in `enabled_macros`.
- Tests for all six Blueprint macros registration (`ServiceProviderTest`).
- Tests for query scopes: `createdBy`, `updatedBy`, `deletedBy`.
- Tests for Eloquent relationships: `creator`, `updater`, `deleter`.
- Tests for `forceDelete` (must not set `deleted_by`).
- Tests for `restoring` event (must clear `deleted_by`).

### Changed

- CI matrix expanded to cover PHP 8.1, 8.2, 8.3 and 8.4 against Laravel `9.*`, `10.*`, `11.*` and `12.*`.
- `$auditableColumnCache` changed from static to instance-level property to prevent stale cache across tests.
- PHP and Laravel version badges updated in README.

### Fixed

- Missing `->index()` on ULID foreign key columns (`created_by`, `updated_by`, `deleted_by`) in `userAuditable` macro.
- `deleting` event no longer performs a raw DB update when `forceDelete()` is called.

### Removed

- Deleted unused `DatabaseRefresh` trait.

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
- Update package version from 1.0.1 to 1.0.2.
- Update this CHANGELOG.md.

## [1.0.1] - 2026-02-02

- Solve `composer require...` issue.
- Update package version from 1.0.0 to 1.0.1.

## [1.0.0] - 2025-10-15

### Added

- Initial release! 🎉
- User auditing macros: `userAuditable()`, `fullAuditable()`, etc.
- `UserAuditable` trait for automatic user tracking.
- Support for ID, UUID, and ULID key types.
- Query scopes: `createdBy()`, `updatedBy()`, `deletedBy()`.
- Relationships: `creator()`, `updater()`, `deleter()`.
- Comprehensive configuration file.
- Service provider with auto-discovery.

### Technical

- PSR-4 autoloading.
- Laravel package auto-discovery.
- Configuration publishing.
- MIT License.
