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

    /*
    |--------------------------------------------------------------------------
    | Platform Contact Information
    |--------------------------------------------------------------------------
    |
    | Contact details for the platform. Used when customers want to inquire
    | about items that are with sellers (not in branches).
    |
    */

    'whatsapp' => [
        'number' => env('WHATSAPP_NUMBER', '+963959378002'),
    ],

    'telegram' => [
        'username' => env('TELEGRAM_USERNAME', 'KARMO_VSKY'),
        'contact' => env('TELEGRAM_CONTACT', '+963959378002'),
    ],

    'platform' => [
        'whatsapp' => env('PLATFORM_WHATSAPP', '+963959378002'),
        'telegram_username' => env('PLATFORM_TELEGRAM_USERNAME', 'khezana_support'),
        'phone' => env('PLATFORM_PHONE', '+963959378002'),
        'email' => env('PLATFORM_EMAIL', 'support@khezana.com'),
    ],

];
