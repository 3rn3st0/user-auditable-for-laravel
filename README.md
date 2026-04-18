# User Auditable for Laravel

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://php.net/)
[![Laravel Version](https://img.shields.io/badge/laravel-%3E%3D9.0-FF2D20.svg)](https://laravel.com)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/ernestoch/user-auditable-for-laravel.svg?style=flat-square)](https://packagist.org/packages/ernestoch/user-auditable-for-laravel)
[![Tests](https://github.com/3rn3st0/user-auditable-for-laravel/actions/workflows/test.yml/badge.svg)](https://github.com/3rn3st0/user-auditable-for-laravel/actions/workflows/test.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/ernestoch/user-auditable-for-laravel.svg?style=flat-square)](https://packagist.org/packages/ernestoch/user-auditable-for-laravel)

A Laravel package that provides user auditing capabilities for your database tables and Eloquent models. Easily track which users create, update, and delete records in your application.

## Features

- 🕵️ **User Auditing**: Automatically track `created_by`, `updated_by`, and `deleted_by`
- 🔧 **Flexible Macros**: Schema macros for easy migration creation
- 🎯 **Multiple Key Types**: Support for ID, UUID, and ULID
- 🏷️ **Relationships**: Built-in relationships to user models
- 📊 **Query Scopes**: Easy filtering by user actions
- 🎭 **Custom Events**: Track any business event with dynamic `EventAuditable` trait
- ⚡ **Zero Configuration**: Works out of the box

## Requirements

- PHP 8.1 or higher
- Laravel 9.0 or higher

> **Laravel 9 notice:** Laravel 9 reached End of Life in February 2024 and carries
> known security advisories. This package declares compatibility with Laravel 9 but
> CI tests for that version may fail due to Composer blocking EOL packages.
> Use Laravel 9 at your own risk.

## Installation

```bash
composer require ernestoch/user-auditable-for-laravel
```

## Configuration

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=user-auditable-config
```

> **Note:** `fullAuditable()` requires `user_auditable` to also be listed in `enabled_macros`.
> If you disable `user_auditable` in the published config, registering `full_auditable` will throw a `RuntimeException` at boot time.

## Usage

### Migrations

Use the provided macros in your migrations:

```php
// Basic usage with default values
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->fullAuditable(); // Adds timestamps, soft deletes, and user auditing
});

// Custom user table and UUID key type
Schema::create('products', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->fullAuditable('admins', 'uuid');
});

// Only user auditing columns (no timestamps or soft deletes)
Schema::create('settings', function (Blueprint $table) {
    $table->string('key')->primary();
    $table->text('value');
    $table->userAuditable('users', 'ulid');
});

// Reversing user auditing columns in a down() migration
Schema::table('posts', function (Blueprint $table) {
    $table->dropUserAuditable(); // Drops foreign keys and audit columns
});
```

#### Custom Event Columns

Use `eventAuditable()` to stamp any custom business event with its own `_at` timestamp
and/or `_by` user FK, reading `user_table` and `key_type` from `config('user-auditable.defaults')`:

```php
// Both columns: released_at (timestamp) + released_by (FK to users)
Schema::table('products', function (Blueprint $table) {
    $table->eventAuditable('released');
});

// Timestamp only: approved_at
Schema::table('orders', function (Blueprint $table) {
    $table->eventAuditable('approved', 'at');
});

// FK only: archived_by
Schema::table('posts', function (Blueprint $table) {
    $table->eventAuditable('archived', 'by');
});

// Reversing in a down() migration
Schema::table('products', function (Blueprint $table) {
    $table->dropEventAuditable('released'); // Drops FK + both columns
});
```

### Models

Use the `UserAuditable` trait in your Eloquent models:

```php
<?php

namespace App\Models;

use ErnestoCh\UserAuditable\Traits\UserAuditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes, UserAuditable;

    protected $fillable = [
        'title',
        'content',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
```

### Relationships

The trait automatically provides relationships:

```php
$post = Post::first();

// Get the user who created the post
$creator = $post->creator;

// Get the user who updated the post
$updater = $post->updater;

// Get the user who deleted the post (if using soft deletes)
$deleter = $post->deleter;
```

### Query Scopes

Filter records by user actions:

```php
// Get all posts created by user with ID 1
$posts = Post::createdBy(1)->get();

// Get all posts updated by user with ID 2
$posts = Post::updatedBy(2)->get();

// Get all posts deleted by user with ID 3
$posts = Post::deletedBy(3)->get();
```

### Custom Event Tracking

Use the `EventAuditable` trait in your models to dynamically access custom events defined via the `eventAuditable()` macro:

```php
<?php

namespace App\Models;

use ErnestoCh\UserAuditable\Traits\EventAuditable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use EventAuditable;
}
```

Then access event relationships and timestamps dynamically:

```php
$product = Product::first();

// Get user who released the product
$releasedBy = $product->releasedBy(); // BelongsTo User

// Get when it was released
$releasedAt = $product->releasedAt(); // Carbon timestamp

// Filter products by event user
$released = Product::eventBy('released', $userId)->get();
```

## Available Macros

| Macro | Description | Parameters |
| --- | --- | --- |
| userAuditable() | Adds user auditing columns | ?string $userTable = null, ?string $keyType = null |
| dropUserAuditable() | Removes user auditing columns | bool $dropForeign = true |
| fullAuditable() | Adds timestamps, soft deletes, and user auditing | ?string $userTable = null, ?string $keyType = null |
| dropFullAuditable() | Removes timestamps, soft deletes, and user auditing | bool $dropForeign = true |
| uuidColumn() | Adds UUID column | string $columnName = 'uuid' |
| dropUuidColumn() | Removes UUID column | string $columnName = 'uuid' |
| ulidColumn() | Adds ULID column | string $columnName = 'ulid' |
| dropUlidColumn() | Removes ULID column | string $columnName = 'ulid' |
| statusColumn() | Adds status enum column | string $columnName = 'status', array $allowed = ['active','inactive','pending'], string $default = 'active' |
| dropStatusColumn() | Removes status column | string $columnName = 'status' |
| eventAuditable() | Adds a custom event timestamp and/or user FK | string $event, ?string $column = null |
| dropEventAuditable() | Removes custom event columns | string $event, ?string $column = null, bool $dropForeign = true |

## Testing

### Setup

A `.env.testing.example` file is included in the repository as a reference. Copy it and fill in your local values:

```bash
# Linux / macOS
cp .env.testing.example .env.testing

# Windows
copy .env.testing.example .env.testing
```

> ⚠️ **Never commit `.env.testing` to the repository.** It is already listed in `.gitignore`.

### Running with MySQL

Set the following environment variables and fill in your values in the `.env.testing` file:

```env
TEST_DB_HOST=127.0.0.1
TEST_DB_PORT=3306
TEST_DB_DATABASE=test_database
TEST_DB_USERNAME=root
TEST_DB_PASSWORD=your-local-mysql-password-here
```

Then set `DB_CONNECTION` in your terminal session:

```bash
# Linux / macOS
export DB_CONNECTION=mysql

# Windows
set DB_CONNECTION=mysql
```

### Running with SQLite (default)

No additional configuration is needed. SQLite in-memory is the default driver used by `phpunit.xml`.

```bash
# Linux / macOS
./vendor/bin/phpunit

# Windows
vendor\bin\phpunit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email [ernestochapon@gmail.com](mailto:ernestochapon@gmail.com) instead of using the issue tracker.

## Credits

Author: [Ernesto Chapon](https://github.com/3rn3st0).

All contributors (⚠️ Not available yet).

## License

The MIT License (MIT). Please see [License](LICENSE.md) File for more information.
