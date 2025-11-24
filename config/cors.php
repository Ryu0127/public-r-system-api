<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins for Domain Restriction
    |--------------------------------------------------------------------------
    |
    | 特定のAPIエンドポイントに対してドメイン制限を適用する際に使用します。
    | カンマ区切りで複数のドメインを指定できます。
    | 例: http://localhost:3000,https://example.com
    |
    */
    'allowed_origins_for_restriction' => env('ALLOWED_ORIGINS', ''),

];
