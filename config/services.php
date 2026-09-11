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

    'snippe' => [
        'api_key' => env('SNIPPE_API_KEY', 'snp_d72351fa5858490448258a2d515e5a8ad2439fca3ddf64a2123737fbc1c28ce8'),
        'webhook_secret' => env('SNIPPE_WEBHOOK_SECRET', 'whsec_0836d02c3d08337fe6597f9c199e2d82c98fea6c718cf3669bc08dc9b6485cf6'),
        'base_url' => env('SNIPPE_BASE_URL', 'https://api.snippe.sh/api/v1'),
        'webhook_url' => env('SNIPPE_WEBHOOK_URL', 'https://fundi-app-one.vercel.app/api/webhook/snippe'),
    ],

];
