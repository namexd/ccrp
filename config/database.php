<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => 'InnoDB ROW_FORMAT=DYNAMIC',
        ],

        'dbyingyong' => [
            'driver' =>  env('DB_YINGYONG_CONNECTION','mysql'),
            'host' => env('DB_YINGYONG_HOST', '127.0.0.1'),
            'port' => env('DB_YINGYONG_PORT', '3306'),
            'database' => env('DB_YINGYONG_DATABASE', 'forge'),
            'username' => env('DB_YINGYONG_USERNAME', 'forge'),
            'password' => env('DB_YINGYONG_PASSWORD', ''),
            'prefix' => env('DB_YINGYONG_PREFIX', ''),
            'strict' => false,
            'engine' => null,
        ],
        'dbcaiji' => [
            'driver' =>  env('DB_CAIJI_CONNECTION','mysql'),
            'host' => env('DB_CAIJI_HOST', '127.0.0.1'),
            'port' => env('DB_CAIJI_PORT', '3306'),
            'database' => env('DB_CAIJI_DATABASE', 'forge'),
            'username' => env('DB_CAIJI_USERNAME', 'forge'),
            'password' => env('DB_CAIJI_PASSWORD', ''),
            'prefix' => env('DB_CAIJI_PREFIX', ''),
            'strict' => true,
            'engine' => null,
        ],
        'pgyingyong' => [
            'driver' => env('PG_YINGYONG_CONNECTION','pgsql'),
            'host' => env('PG_YINGYONG_HOST', '127.0.0.1'),
            'port' => env('PG_YINGYONG_PORT', '5432'),
            'database' => env('PG_YINGYONG_DATABASE', 'forge'),
            'username' => env('PG_YINGYONG_USERNAME', 'forge'),
            'password' => env('PG_YINGYONG_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' =>  env('PG_YINGYONG_PREFIX', ''),
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
        'dbhistory' => [
            'driver' => env('DB_HISTORY_CONNECTION','pgsql'),
            'host' => env('DB_HISTORY_HOST', '127.0.0.1'),
            'port' => env('DB_HISTORY_PORT', '5432'),
            'database' => env('DB_HISTORY_DATABASE', 'forge'),
            'username' => env('DB_HISTORY_USERNAME', 'forge'),
            'password' => env('DB_HISTORY_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' =>  env('DB_HISTORY_PREFIX', ''),
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
        'pgccsc' => [
            'driver' => env('PG_CCSC_CONNECTION','pgsql'),
            'host' => env('PG_CCSC_HOST', '127.0.0.1'),
            'port' => env('PG_CCSC_PORT', '5432'),
            'database' => env('PG_CCSC_DATABASE', 'forge'),
            'username' => env('PG_CCSC_USERNAME', 'forge'),
            'password' => env('PG_CCSC_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' =>  env('PG_CCSC_PREFIX', ''),
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
        'dbocenter' => array(
            'driver' =>  env('DB_OCENTER_CONNECTION','mysql'),
            'host' => env('DB_OCENTER_HOST', '127.0.0.1'),
            'port' => env('DB_OCENTER_PORT', '3306'),
            'database' => env('DB_OCENTER_DATABASE', 'forge'),
            'username' => env('DB_OCENTER_USERNAME', 'forge'),
            'password' => env('DB_OCENTER_PASSWORD', ''),
            'prefix' => env('DB_OCENTER_PREFIX', ''),
            'strict' => true,
            'engine' => null,
        ),
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
