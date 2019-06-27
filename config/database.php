<?php

return [
    "database" =>[
        "connections" => [
            "sc00002" => [
                'driver'    => 'mysql',
                'host'      => getenv('DB_HOST'),
                'port'      => getenv('DB_PORT'),
                'database' =>  getenv('DB_DATABASE'),
                'username'  => getenv('DB_USERNAME'),
                'password'  => getenv('DB_PASSWORD'),
                'charset'   => getenv('DB_CHARSET'),
                'collation' => getenv('DB_COLLATION'),
                'prefix'    => getenv('DB_PREFIX'),
            ],
        ]
    ]
];
