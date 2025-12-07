# User Auditable for Laravel

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://php.net/)
[![Laravel Version](https://img.shields.io/badge/laravel-%3E%3D9.0-FF2D20.svg)](https://laravel.com)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/ernestoch/laravel-user-auditable.svg?style=flat-square)](https://packagist.org/packages/ernestoch/user-auditable-for-laravel)
[![Tests](https://github.com/3rn3st0/user-auditable-for-laravel/actions/workflows/test.yml/badge.svg)](https://github.com/3rn3st0/user-auditable-for-laravel/actions/workflows/test.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/ernestoch/laravel-user-auditable.svg?style=flat-square)](https://packagist.org/packages/ernestoch/user-auditable-for-laravel)

A Laravel package that provides user auditing capabilities for your database tables and Eloquent models. Easily track which users create, update, and delete records in your application.

## Features

- 🕵️ **User Auditing**: Automatically track `created_by`, `updated_by`, and `deleted_by`
- 🔧 **Flexible Macros**: Schema macros for easy migration creation
- 🎯 **Multiple Key Types**: Support for ID, UUID, and ULID
- 🏷️ **Relationships**: Built-in relationships to user models
- 📊 **Query Scopes**: Easy filtering by user actions
- ⚡ **Zero Configuration**: Works out of the box

## Requirements

- PHP 8.1 or higher
- Laravel 9.0 or higher

## Installation

```bash
composer require 3rn3st0/user-auditable-for-laravel
```

## Configuration

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=user-auditable-config
```

## Usage

### Migrations

Use the provided macros in your migrations:

```php
// Basic usage with default values
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->fullAuditable(); // Uses 'users' table and 'id' key type
});

// Custom user table and UUID key type
Schema::create('products', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->fullAuditable('admins', 'uuid');
});

// Only user auditing columns
Schema::create('settings', function (Blueprint $table) {
    $table->string('key')->primary();
    $table->text('value');
    $table->userAuditable('users', 'ulid');
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

## Available Macros

| Macro               | Description                                      | Parameters                                  |
|---------------------|--------------------------------------------------|---------------------------------------------|
| userAuditable()     | Adds user auditing columns                       | $userTable = 'users', $keyType = 'id'       |
| dropUserAuditable() | Removes user auditing columns                    | $dropForeign = true                         |
| fullAuditable()     | Adds timestamps, soft deletes, and user auditing | $userTable = 'users', $keyType = 'id'       |
| uuidColumn()        | Adds UUID column                                 | $columnName = 'uuid'                        |
| ulidColumn()        | Adds ULID column                                 | $columnName = 'ulid'                        |
| statusColumn()      | Adds status enum column                          | $columnName = 'status', $default = 'active' |

## Testing

To successfully run the tests using MySQL driver, you must set the ``DB_CONNECTION`, `DB_DATABASE` (It MUST be declared as shown below) and `TEST_DB_PASSWORD` environment variables from a terminal as follows:

```bash
set DB_CONNECTION=mysql
set DB_DATABASE=
set TEST_DB_PASSWORD=Your-local-MySQL-password
```

For SQLite driver, `TEST_DB_PASSWORD` is not required. Set same environment variables `DB_CONNECTION` and `DB_DATABASE` from a terminal as follows:

```bash
set DB_CONNECTION=sqlite
set DB_DATABASE=:mmory:
```

Only then will the tests be able to run.

```bash
./vendor/bin/phpunit        // In Linux or macOS

vendor\bin\phpunit          // In Windows
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details (⚠️ Not available yet).

## Security

If you discover any security related issues, please email ernestochapon@gmail.com instead of using the issue tracker.

## Credits

Author: [Ernesto Chapon](https://github.com/3rn3st0).

All contributors (⚠️ Not available yet).

## License

The MIT License (MIT). Please see [License](LICENSE.md) File for more information.
