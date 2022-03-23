<?php
return [

    /*
    |-------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |-------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths'                    => ['api/*', 'web/*'],
    'allowed_methods'          => ['*'],
    'allowed_origins'          => ['https://sokapp5.teammodel.net', 'https://sokapp5.teammodel.cn', 'https://sokapp5-rc.teammodel.net', 'https://sokapp5-rc.teammodel.cn', 'https://192.168.0.193:25534', 'https://localhost:25534'],
    'allowed_origins_patterns' => [],
    'allowed_headers'          => ['*'],
    'exposed_headers'          => [],
    'max_age'                  => 0,
    'supports_credentials'     => true,

];
