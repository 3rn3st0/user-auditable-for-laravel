<?php

namespace ErnestoCh\UserAuditable\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait EventAuditable
{
    /**
     * Cache for database column existence checks
     */
    protected array $auditableColumnCache = [];

    /**
     * Handle dynamic event methods like releasedBy(), approvedAt(), etc.
     */
    public function __call($method, $arguments)
    {
        // Match pattern: eventBy() or eventAt()
        if (preg_match('/^([a-z]+)(By|At)$/', $method, $matches)) {
            $event = $matches[1];
            $type = $matches[2];

            if ($type === 'By') {
                return $this->getEventUser($event);
            } elseif ($type === 'At') {
                return $this->getEventTimestamp($event);
            }
        }

        throw new \BadMethodCallException("Call to undefined method {$method}");
    }

    /**
     * Handle dynamic query scopes like releasedBy($userId), approvedBy($userId), etc.
     */
    public static function __callStatic($method, $arguments)
    {
        // Match pattern: eventBy($userId)
        if (preg_match('/^([a-z]+)By$/', $method, $matches)) {
            $event = $matches[1];
            $userId = $arguments[0] ?? null;

            if ($userId === null) {
                throw new \BadMethodCallException("User ID required for {$method}");
            }

            $column = "{$event}_by";
            return static::query()->where($column, $userId);
        }

        throw new \BadMethodCallException("Call to undefined static method {$method}");
    }

    /**
     * Get the user who performed the event
     */
    protected function getEventUser(string $event): ?BelongsTo
    {
        $column = "{$event}_by";
        
        if (!$this->hasEventColumn($column)) {
            return null;
        }

        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        return $this->belongsTo($userModel, $column);
    }

    /**
     * Get the timestamp of when the event occurred
     */
    protected function getEventTimestamp(string $event)
    {
        $column = "{$event}_at";
        
        if (!$this->hasEventColumn($column)) {
            return null;
        }

        return $this->{$column};
    }

    /**
     * Check if a specific column exists in the model's table
     */
    protected function hasEventColumn(string $column): bool
    {
        if (!isset($this->auditableColumnCache[$column])) {
            $this->auditableColumnCache[$column] = Schema::hasColumn($this->getTable(), $column);
        }

        return $this->auditableColumnCache[$column];
    }
}
