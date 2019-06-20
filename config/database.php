<?php

$envSettings = \Noodlehaus\Config::load(BASE_PATH . '/env.php');

return [
    "database" =>[
        "connections" => [
            "sc00002" => [
                'driver'    => 'mysql',
                'host'      => $envSettings->get('DB_HOST'),
                'port'      => $envSettings->get('DB_PORT'),
                'database' =>  $envSettings->get('DB_DATABASE'),
                'username'  => $envSettings->get('DB_USERNAME'),
                'password'  => $envSettings->get('DB_PASSWORD'),
                'charset'   => $envSettings->get('DB_CHARSET'),
                'collation' => $envSettings->get('DB_COLLATION'),
                'prefix'    => $envSettings->get('DB_PREFIX'),
            ],
        ]
    ]
];
