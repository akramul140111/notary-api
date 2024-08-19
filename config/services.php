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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'doptor' => [
        'login_url' => env('DOPTOR_URL'),
        'nothi_api_link' => env('NOTHI_API_LINK'),
        'doptor_park_url' => env('DOPTOR_PARK_URL'),
    ],
    'app' => [
        'call_back' => env('CALL_BACK'),
        'kong_api_key' => env('KONG_API_KEY'),
        'token_email' => env('TOKEN_EMAIL'),
        'token_password' => env('TOKEN_PASSWORD'),
        'token_url' => env('TOKEN_URL'),
        'service_url' => env('SERVICE_URL'),
        'mygov_form_attachment_url' => env('MYGOV_FORM_ATTACHMENTS'),
        'service_cache_time' => env('SERVICE_CACHE_TIME'),
        'mygov_external_application_url' => env('MYGOV_EXTERNAL_APPLICATION'),
        'base_url' => env('APP_URL'),
    ],

];
