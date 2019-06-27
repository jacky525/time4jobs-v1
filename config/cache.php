<?php

return [
    'cache' => [
        'path' => [
            'router' => APP_ROOT . '/cache/routes.php',
            'filesystemCache' => APP_ROOT . '/cache/symfony-cache',
        ],
        /*
         * class name => cache key => cache time (sec)
         */
        TestService::class => [
            'ttl' => 10,
        ],
    ]
];
