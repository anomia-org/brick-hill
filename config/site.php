<?php

return [
    'storage' => [
        'domain' => env('STORAGE_DOMAIN', 'https://brkcdn.com'),
        'thumbnails' => env('THUMBNAIL_DOMAIN', 'https://thumbnails.brkcdn.com'),
        'pending' => [
            '512' => env('STORAGE_DOMAIN', 'https://brkcdn.com') . env('STORAGE_PENDING_512', '/default/pending.png'),
            'set' => env('STORAGE_DOMAIN', 'https://brkcdn.com') . env('STORAGE_PENDING_SET', '/default/pendingset.png'),
        ],
        'declined' => [
            '512' => env('STORAGE_DOMAIN', 'https://brkcdn.com') . env('STORAGE_DECLINED_512', '/default/declined.png'),
            'set' => env('STORAGE_DOMAIN', 'https://brkcdn.com') . env('STORAGE_DECLINED_SET', '/default/declinedset.png'),
        ],
        'avatars' => env('STORAGE_DOMAIN', 'https://brkcdn.com') . env('STORAGE_AVATARS_LOC', '/images/avatars/'),
        'items' => env('STORAGE_DOMAIN', 'https://brkcdn.com') . env('STORAGE_ITEMS_LOC', '/v2/images/shop/thumbnails/'),
        'file' => env('STORAGE_FILE_LOC', '/var/www/storage_subdomain/v2/assets')
    ],
    'avatar' => [
        'default_colors' => [
            'head' => 'f3b700',
            'torso' => '85ad00',
            'right_arm' => 'f3b700',
            'left_arm' => 'f3b700',
            'right_leg' => '76603f',
            'left_leg' => '76603f'
        ],
        'default_items' => [
            'hats' => [0, 0, 0, 0, 0],
            'face' => 0,
            'tool' => 0,
            'head' => 0,
            'figure' => 0,
            'shirt' => 0,
            'pants' => 0,
            'tshirt' => 0,
            'clothing' => [],
        ]
    ],
    'maintenance' => [
        'key' => env('MAINTENANCE_KEY')
    ],
    'captcha' => [
        'recaptcha' => [
            'secret' => env('RECAPTCHA_SECRET'),
            'key' => env('RECAPTCHA_PUBLIC')
        ],
        'hcaptcha' => [
            'secret' => env('HCAPTCHA_SECRET'),
            'key' => env('HCAPTCHA_PUBLIC')
        ],
    ],
    'keys' => [
        'manager' => env('SERVER_MANAGER_PRIVATE')
    ],
    'logs' => [
        'db_queries' => env('LOG_DB_QUERIES', false)
    ],
    'sqs' => [
        'manager_queue' => env('MANAGER_QUEUE')
    ],
    'payments' => [
        'mode' => (env('APP_ENV') == 'prod' || env('APP_ENV') == 'production') ? 'live' : 'testing',
        'stripe' => [
            'live' => [
                'api_key_public' => env('STRIPE_LIVE_KEY_PUBLIC'),
                'api_key_secret' => env('STRIPE_LIVE_KEY_SECRET'),
                'webhook_secret' => env('STRIPE_LIVE_WEBHOOK_SECRET'),
            ],
            'testing' => [
                'api_key_public' => env('STRIPE_TESTING_KEY_PUBLIC'),
                'api_key_secret' => env('STRIPE_TESTING_KEY_SECRET'),
                'webhook_secret' => env('STRIPE_TESTING_WEBHOOK_SECRET'),
            ]
        ],
        'paypal' => [
            'subdomain' => (env('APP_ENV') == 'prod' || env('APP_ENV') == 'production') ? 'api.paypal.com' : 'api.sandbox.paypal.com',
            'live' => [
                'api_username' => env('PAYPAL_LIVE_API_USERNAME'),
                'api_key_public' => env('PAYPAL_LIVE_API_PUBLIC'),
                'api_key_secret' => env('PAYPAL_LIVE_API_SECRET'),
                'webhook_id' => env('PAYPAL_LIVE_WEBHOOK_ID'),
            ],
            'testing' => [
                'api_username' => env('PAYPAL_TESTING_API_USERNAME'),
                'api_key_public' => env('PAYPAL_TESTING_API_PUBLIC'),
                'api_key_secret' => env('PAYPAL_TESTING_API_SECRET'),
                'webhook_id' => env('PAYPAL_TESTING_WEBHOOK_ID'),
            ]
        ]
    ],
    'main_account_id' => env('MAIN_ACCOUNT_ID', 1003),
    'url' => env('APP_URL', 'https://www.brick-hill.com'),
    'api_url' => env('API_URL'),
    'admin_url' => env('ADMIN_URL'),
];
