<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;
use Dedoc\Scramble\SecurityDocumentation\MiddlewareAuthSecurityStrategy;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

return [
    'api_path' => 'api',
    'api_domain' => null,
    'export_path' => 'api.json',
    'cache' => [
        'key' => 'scramble.openapi',
        'store' => 'file',
    ],
    'info' => [
        'version' => env('API_VERSION', '0.0.1'),
        'description' => 'Aparthub resident mobile authentication and profile API.',
    ],
    'ui' => [
        'title' => 'Aparthub API Docs',
    ],
    'renderer' => 'elements',
    'servers' => null,
    'middleware' => [
        'web',
        RestrictedDocsAccess::class,
    ],
    'security_strategy' => [
        MiddlewareAuthSecurityStrategy::class,
        [
            'middleware' => ['auth:sanctum'],
            'scheme' => SecurityScheme::http('bearer'),
        ],
    ],
];
