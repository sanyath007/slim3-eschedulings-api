<?php

return [
    'settings' => [
        'addContentLengthHeader' => false,
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true,
        'db' => [
            'driver'    => getenv("DB_DRIVER"),
            'host'      => getenv("DB_HOST"),
            'database'  => getenv("DB_NAME"),
            'username'  => getenv("DB_USER"),
            'password'  => getenv("DB_PASS"),
            'port'      => getenv("DB_PORT"),
            'charset'   => getenv("DB_CHARSET"), //utf8, tis620
            'collation' => getenv("DB_COLLATE"), //utf8_general_ci, tis620_thai_ci
            'prefix'    => getenv("DB_PREFIX"),
            'options' => [
                // Turn off persistent connections
                PDO::ATTR_PERSISTENT => false,
                // Enable exceptions
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // Emulate prepared statements
                PDO::ATTR_EMULATE_PREPARES => true,
                // Set default fetch mode to array
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Set character set
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' .getenv("DB_CHARSET"). ' COLLATE ' .getenv("DB_COLLATE")
            ],
        ],
        'db_person' => [
            'driver'    => getenv("DB_PER_DRIVER"),
            'host'      => getenv("DB_PER_HOST"),
            'database'  => getenv("DB_PER_NAME"),
            'username'  => getenv("DB_PER_USER"),
            'password'  => getenv("DB_PER_PASS"),
            'port'      => getenv("DB_PER_PORT"),
            'charset'   => getenv("DB_PER_CHARSET"), //utf8, tis620
            'collation' => getenv("DB_PER_COLLATE"), //utf8_general_ci, tis620_thai_ci
            'prefix'    => getenv("DB_PER_PREFIX"),
            'options' => [
                // Turn off persistent connections
                PDO::ATTR_PERSISTENT => false,
                // Enable exceptions
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // Emulate prepared statements
                PDO::ATTR_EMULATE_PREPARES => true,
                // Set default fetch mode to array
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Set character set
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' .getenv("DB_CHARSET"). ' COLLATE ' .getenv("DB_COLLATE")
            ],
        ],
    ]
];