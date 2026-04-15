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

    'supabase' => [
        /*
        |--------------------------------------------------------------------------
        | Supabase URL
        |--------------------------------------------------------------------------
        |
        | The URL of your Supabase project. This is used to construct the JWKS
        | endpoint and verify JWT issuer claims.
        |
        | Example: https://your-project.supabase.co
        */
        'url' => env('SUPABASE_URL'),

        /*
        |--------------------------------------------------------------------------
        | Supabase Anon Key
        |--------------------------------------------------------------------------
        |
        | The anonymous/public key for your Supabase project.
        | This is safe to expose in client-side code.
        |
        | Note: We do NOT use this for authentication on the backend.
        | Backend uses JWT verification with public keys from JWKS.
        */
        'anon_key' => env('SUPABASE_ANON_KEY'),

        /*
        |--------------------------------------------------------------------------
        | Supabase Service Role Key
        |--------------------------------------------------------------------------
        |
        | The service role key for your Supabase project.
        | KEEP THIS SECRET - never expose in client-side code.
        |
        | Used for admin operations on Supabase (e.g., managing users).
        */
        'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY'),
    ],

];
