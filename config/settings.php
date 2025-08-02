<?php

return [
    'default_repository' => 'database',

    'repositories' => [
        'database' => [
            'type' => Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository::class,
            'connection' => null,
            'table' => 'settings',
        ],
    ],
];