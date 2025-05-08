<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth','api/broadcasting/auth', 'login', 'logout'],
  
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:8000',  // your frontend
        'http://127.0.0.1:8000', // your frontend
        'http://localhost:5173',
        'http://localhost:3000',  // your Vue dev server
    ],
    'allowed_headers' => ['*'],
    'allowed_origins_patterns' => [],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];


