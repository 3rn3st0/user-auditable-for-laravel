<?php

namespace ErnestoCh\UserAuditable\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use InvalidArgumentException;

class UserAuditableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishConfig();
            $this->publishMigrations();
        }

        $this->registerMacros();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/user-auditable.php',
            'user-auditable'
        );
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__.'/../../config/user-auditable.php' => config_path('user-auditable.php'),
        ], 'user-auditable-config');
    }

    protected function publishMigrations(): void
    {
        $this->publishes([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ], 'user-auditable-migrations');
    }

    protected function registerMacros(): void
    {
        $enabledMacros = config('user-auditable.enabled_macros', []);

        foreach ($enabledMacros as $macro) {
            $methodName = Str::camel($macro);
            if (method_exists($this, $methodName)) {
                $this->{$methodName}();
            }
        }
    }

    protected function userAuditable(): void
    {
        Blueprint::macro('userAuditable', function (
            ?string $userTable = null,
            ?string $keyType = null
        ) {
            /** @var Blueprint $this */

            $userTable = $userTable ?? config('user-auditable.defaults.user_table', 'users');
            $keyType   = $keyType   ?? config('user-auditable.defaults.key_type', 'id');

            $validKeyTypes = ['id', 'uuid', 'ulid'];
            if (!in_array($keyType, $validKeyTypes)) {
                throw new InvalidArgumentException(
                    "Invalid key type: {$keyType}. Must be one of: " . implode(', ', $validKeyTypes)
                );
            }

            if (empty($userTable)) {
                throw new InvalidArgumentException('User table name cannot be empty');
            }

            switch ($keyType) {
                case 'uuid':
                    $this->foreignUuid('created_by')
                         ->nullable()
                         ->index()
                         ->constrained($userTable)
                         ->onDelete('set null');
                    $this->foreignUuid('updated_by')
                         ->nullable()
                         ->index()
                         ->constrained($userTable)
                         ->onDelete('set null');
                    $this->foreignUuid('deleted_by')
                         ->nullable()
                         ->index()
                         ->constrained($userTable)
                         ->onDelete('set null');
                    break;

                case 'ulid':
                    $this->ulid('created_by')->nullable()->index();
                    $this->ulid('updated_by')->nullable()->index();
                    $this->ulid('deleted_by')->nullable()->index();

                    $this->foreign('created_by')->references('id')->on($userTable)->onDelete('set null');
                    $this->foreign('updated_by')->references('id')->on($userTable)->onDelete('set null');
                    $this->foreign('deleted_by')->references('id')->on($userTable)->onDelete('set null');
                    break;

                default: // id
                    $this->foreignId('created_by')
                         ->nullable()
                         ->index()
                         ->constrained($userTable)
                         ->onDelete('set null');
                    $this->foreignId('updated_by')
                         ->nullable()
                         ->index()
                         ->constrained($userTable)
                         ->onDelete('set null');
                    $this->foreignId('deleted_by')
                         ->nullable()
                         ->index()
                         ->constrained($userTable)
                         ->onDelete('set null');
            }

            return $this;
        });
    }

    protected function dropUserAuditable(): void
    {
        Blueprint::macro('dropUserAuditable', function (bool $dropForeign = true) {
            /** @var Blueprint $this */

            if ($dropForeign) {
                $this->dropForeign(['created_by']);
                $this->dropForeign(['updated_by']);
                $this->dropForeign(['deleted_by']);
            }

            $this->dropColumn(['created_by', 'updated_by', 'deleted_by']);

            return $this;
        });
    }

    protected function uuidColumn(): void
    {
        Blueprint::macro('uuidColumn', function (string $columnName = 'uuid') {
            /** @var Blueprint $this */
            $this->uuid($columnName)->unique()->index();
            return $this;
        });
    }

    protected function ulidColumn(): void
    {
        Blueprint::macro('ulidColumn', function (string $columnName = 'ulid') {
            /** @var Blueprint $this */
            $this->ulid($columnName)->unique()->index();
            return $this;
        });
    }

    protected function statusColumn(): void
    {
        Blueprint::macro('statusColumn', function (string $columnName = 'status', string $default = 'active') {
            /** @var Blueprint $this */
            $this->enum($columnName, ['active', 'inactive', 'pending'])->default($default);
            return $this;
        });
    }

    protected function fullAuditable(): void
    {
        Blueprint::macro('fullAuditable', function (
            ?string $userTable = null,
            ?string $keyType = null
        ) {
            /** @var Blueprint $this */
            $userTable = $userTable ?? config('user-auditable.defaults.user_table', 'users');
            $keyType   = $keyType   ?? config('user-auditable.defaults.key_type', 'id');

            $this->timestamps();
            $this->softDeletes();
            $this->userAuditable($userTable, $keyType);
            return $this;
        });
    }
}
