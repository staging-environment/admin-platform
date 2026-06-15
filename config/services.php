<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-6'),
        'max_tokens' => env('ANTHROPIC_MAX_TOKENS', 1024),
    ],

    'miteco' => [
        'firma' => env('MITECO_FIRMA', 'IND'),
        'user' => env('MITECO_USER'),
        'password' => env('MITECO_PASSWORD'),
        'stations' => [
            'UTRERA' => [
                'user' => env('MITECO_UTRERA_USER'),
                'password' => env('MITECO_UTRERA_PASS'),
            ],
            'RONDA_NORTE' => [
                'user' => env('MITECO_RONDA_NORTE_USER'),
                'password' => env('MITECO_RONDA_NORTE_PASS'),
            ],
            'EL_CUERVO' => [
                'user' => env('MITECO_EL_CUERVO_USER'),
                'password' => env('MITECO_EL_CUERVO_PASS'),
            ],
            'LEBRIJA' => [
                'user' => env('MITECO_LEBRIJA_USER'),
                'password' => env('MITECO_LEBRIJA_PASS'),
            ],
        ],
    ],

];
