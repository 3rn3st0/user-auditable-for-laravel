<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enabled Macros
    |--------------------------------------------------------------------------
    |
    | Here you can specify which schema macros are enabled when the package
    | is loaded. You can disable any macro that you don't plan to use.
    |
    */
    'enabled_macros' => [
        'user_auditable',
        'drop_user_auditable',
        'full_auditable',
        'drop_full_auditable',
        'uuid_column',
        'drop_uuid_column',
        'ulid_column',
        'drop_ulid_column',
        'status_column',
        'drop_status_column',
        'event_auditable',
        'drop_event_auditable',
        'audit_log_table',
        'drop_audit_log_table',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    |
    | These values are used as defaults when calling the macros without
    | parameters. You can customize them according to your application needs.
    |
    */
    'defaults' => [
        'user_table' => 'users',
        'key_type' => 'id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Change Tracking
    |--------------------------------------------------------------------------
    |
    | Configuration for the optional ChangeAuditable trait.
    |
    */
    'change_tracking' => [
        'enabled' => true,
        'table' => 'audit_logs',
        'retain_days' => null,
        'log_created' => true,
        'log_updated' => true,
        'log_deleted' => true,
        'log_restored' => true,
        'user_resolver' => null,
        'user_type_resolver' => null,
    ],
];
