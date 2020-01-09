<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notification email (From)
    |--------------------------------------------------------------------------
    |
    | This email is used for sending drop status notifications to PM users
    |
    */
    'alerts' => [

        'email' => [
            'from' => 'no-reply@alerts.profitminer.io',
            'name' => 'ProfitMiner Alerts Service',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Template factory configuration
    |--------------------------------------------------------------------------
    |
    | Here you can replace your exact drop template implementation for
    | SMS and Email.
    |
    | The default one is based on Twig.
    |
    */

    'templates' => [

        'sms' => [
            'class' => \ProfitMiner\Base\Services\Drops\Templates\Twig\SMSTemplate::class
        ],

        'email' => [
            'class' => \ProfitMiner\Base\Services\Drops\Templates\Twig\EmailTemplate::class,

            'from' => [
                'domain' => env('MAILGUN_DOMAIN'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Processors
    |--------------------------------------------------------------------------
    |
    | This config contains configuration for DropProcessorFactory and processor
    | implementations.
    |
    | You can replace the default implementations with your own classes
    |
    */
    'processors' => [

        'email' => [

            'class' => env('MAIL_DROP_PROCESSOR', 'ProfitMiner\Base\Services\Media\Transport\DummyEmailTransport'),

            // Default chunk size for batch processing
            'chunk_size' => 500,
        ],

        'sms' => [

            'class' => env('SMS_DROP_PROCESSOR', 'ProfitMiner\Base\Services\Media\Transport\DummySMSTransport'),
        ],

        'mailer' => [
        ]
    ],
];
