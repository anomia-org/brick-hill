<?php

use Monolog\Handler\StreamHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'cloudwatch'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'cloudwatch' => [
            'driver' => 'custom',
            'via' => \App\Logging\CloudwatchLogger::class,
            'aws' => [
                'region' => env('AWS_DEFAULT_REGION')
            ]
        ],

        'webhook_failure' => [
            'driver' => 'custom',
            'via' => \App\Logging\CloudwatchLogger::class,
            'aws' => [
                'region' => env('AWS_DEFAULT_REGION')
            ],
            'name' => 'webhook_failure',
        ],

        'renderer' => [
            'driver' => 'custom',
            'via' => \App\Logging\CloudwatchLogger::class,
            'aws' => [
                'region' => env('AWS_DEFAULT_REGION')
            ],
            'name' => 'renderer',
        ],

        'info' => [
            'driver' => 'single',
            'path' => storage_path('logs/info.log'),
            'level' => 'debug',
        ],

        /*'deprecations' => [
            'driver' => 'single',
            'path' => storage_path('logs/php-deprecation-warnings.log'),
        ],*/

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'emergency' => [
            'driver' => 'monolog',
            'path' => 'php://stderr'
        ],
    ],

];
