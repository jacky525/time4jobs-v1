<?php

return [
    'cache' => [
        'path' => [
            'router' => dirname(__DIR__) . '/cache/routes.php',
            'filesystemCache' => dirname(__DIR__) . '/cache/symfony-cache',
        ],
        /*
         * class name => cache key => cache time (sec)
         */
        TestService::class => [
            'ttl' => 10,
        ],
    ]
];
