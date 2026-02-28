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
        'uuid_column',
        'ulid_column',
        'status_column',
        'full_auditable',
        'event_auditable',
        'drop_event_auditable',
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
];
