<?php

return [
    'layout' => [
        'container' => [
            'width' => 'max-w-7xl',
        ],
        'forms' => [
            'actions' => [
                'alignment' => 'left',
            ],
            'width' => 'full',
        ],
    ],
    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),
    'dark_mode' => [
        'enabled' => true,
    ],
    'navigation' => [
        'collapsible' => true,
    ],
    'auth' => [
        'guard' => env('FILAMENT_AUTH_GUARD', 'web'),
        'pages' => [
            'login' => \Filament\Pages\Auth\Login::class,
        ],
    ],
]; 