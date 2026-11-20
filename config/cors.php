<?php

$allowedOrigins = array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000,http://127.0.0.1:3000')));
$frontendUrl = (string) env('FRONTEND_URL', '');
$frontendParts = parse_url($frontendUrl);
if (!empty($frontendParts['scheme']) && !empty($frontendParts['host'])) {
    $allowedOrigins[] = strtolower($frontendParts['scheme'].'://'.$frontendParts['host'].(isset($frontendParts['port']) ? ':'.$frontendParts['port'] : ''));
}

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_values(array_unique(array_filter($allowedOrigins))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
