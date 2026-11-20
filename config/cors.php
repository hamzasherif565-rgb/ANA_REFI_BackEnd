<?php

$allowedOrigins = array_map('trim', explode(',', (string) env(
    'CORS_ALLOWED_ORIGINS',
    'https://ana-refi-frontend.vercel.app,http://localhost:3000,http://localhost:5173,http://127.0.0.1:3000,http://127.0.0.1:5173'
)));
// Keep the deployed frontend explicitly trusted even when the server has a
// stale CORS_ALLOWED_ORIGINS value left over from local development.
$allowedOrigins[] = 'https://ana-refi-frontend.vercel.app';
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
