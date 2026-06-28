// Source - https://stackoverflow.com/a/78838359
// Posted by Bloody Kheeng
// Retrieved 2026-06-28, License - CC BY-SA 4.0

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

    'paths' => ['api/*', 'api/api/*', 'sanctum/csrf-cookie', '*'],
    // 'paths' => ['*'],

    'allowed_methods' => ['POST', 'GET', 'DELETE', 'PUT', '*'],

    // 'allowed_origins' => ['*'],
    'allowed_origins' => ['https://test.vercel.app/', 'https://aws-0-eu-test-superabase.com/', 'http://localhost:3000/', 'http://localhost:5432/', '*'],

    'allowed_origins_patterns' => ['*'],

    'allowed_headers' => ['X-Custom-Header', 'Upgrade-Insecure-Requests', '*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
