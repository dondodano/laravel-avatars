<?php

return [
    // URL path of the endpoint, e.g. /api/avatar
    'path' => 'api/avatar',

    // Middleware for the endpoint. Keep empty so responses stay cookie-free and cacheable.
    'middleware' => [],

    // Browser/CDN cache lifetime in seconds.
    'cache_ttl' => 31536000,

    'defaults' => [
        'size' => 64,
        'background' => 'random', // hex without '#', or 'random'
        'color' => 'ffffff',
        'length' => 2,
        'font_size' => 0.5,       // ratio of size, 0.1 - 1
        'rounded' => false,
        'bold' => false,
        'uppercase' => true,
        'format' => 'svg',        // svg | png
    ],

    'min_size' => 16,
    'max_size' => 512,
];
