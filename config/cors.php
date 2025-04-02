<?php

return [
    'paths' => ['*', 'sanctum/csrf-cookie'],  // Define the routes where CORS applies
    'allowed_methods' => ['*'],  // Allow all HTTP methods (GET, POST, etc.)
    'allowed_origins' => ['*'],  // Allow all origins (Frontend URLs)
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],  // Allow all headers
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,  // Set to true if using cookies or authentication
];
