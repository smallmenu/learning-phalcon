<?php
return array(
    // app
    'app'       => array(
        'domain' => 'demo.phalcon.dev',
        'url'    => 'http://demo.phalcon.dev',
    ),

    // bootstrap
    'bootstrap'     => array(
        'dispatcher' => array(
            'default' => 'Demo\Controllers',
            'cli'     => 'Demo\Tasks',
        ),
        'namespaces' => array(
            'Demo\Controllers' => APP_DIR . '/Controllers',
            'Demo\Models'      => APP_DIR . '/Models',
            'Demo\Plugins'     => APP_DIR . '/Plugins',
            'Demo\Tasks'       => APP_DIR . '/Tasks',
            'Demo\Library'     => APP_DIR . '/Library',
        )
    ),

    // crypt
    'crypt'     => array(
        'authkey' => '123',
    ),

    // database
    'db'        => array(
        'host'     => SDK_MYSQL_HOST,
        'username' => SDK_MYSQL_USER,
        'password' => SDK_MYSQL_PASS,
        'dbname'   => 'test',
        'charset'  => 'utf8',
    ),

    // cache
    'cache'     => array(
        'adapter'  => 'redis',
        'redis'    => array(
            'host'       => SDK_REDIS_HOST,
            'port'       => SDK_REDIS_PORT,
            'auth'       => SDK_REDIS_AUTH,
            'index'      => SDK_REDIS_INDEX,
            'prefix'     => 'demo:',
            'persistent' => false,
        ),
        'memcache' => array(
            'host'       => SDK_MEMCACHE_HOST,
            'port'       => SDK_MEMCACHE_PORT,
            'prefix'     => 'demo:',
            'persistent' => false,
        ),
        'lifetime' => 3600,
    ),

    // metadata
    'metadata'     => array(
        'adapter'  => 'redis',
        'redis'    => array(
            'host'       => SDK_REDIS_HOST,
            'port'       => SDK_REDIS_PORT,
            'auth'       => SDK_REDIS_AUTH,
            'index'      => SDK_REDIS_INDEX,
            'prefix'     => 'demo:',
            'persistent' => false,
        ),
        'memcache' => array(
            'host'       => SDK_MEMCACHE_HOST,
            'port'       => SDK_MEMCACHE_PORT,
            'prefix'     => 'demo:',
            'persistent' => false,
        ),
        'lifetime' => 3600,
    ),

    // session
    'session'   => array(
        'adapter'  => 'redis',
        'options' => array(
            'cache_limiter' => 'nocache',
            'cookie_lifetime' => 600,
            'cookie_path' => '/',
            'cookie_domain' => SDK_DOMAIN,
            'cookie_httponly' => true,
        ),
        'redis'    => array(
            'host'       => SDK_REDIS_HOST,
            'port'       => SDK_REDIS_PORT,
            'auth'       => SDK_REDIS_AUTH,
            'index'      => SDK_REDIS_INDEX,
            'prefix'     => 'demo:',
            'lifetime'   => 600,
            'persistent' => false,
        ),
        'memcache' => array(
            'host'       => SDK_MEMCACHE_HOST,
            'port'       => SDK_MEMCACHE_PORT,
            'prefix'     => 'DEMO_SESSION:',
            'lifetime'   => 600,
            'persistent' => false,
        ),
    ),

    // redis
    'redis'     => array(
        'host'       => SDK_REDIS_HOST,
        'port'       => SDK_REDIS_PORT,
        'auth'       => SDK_REDIS_AUTH,
        'index'      => SDK_REDIS_INDEX,
        'prefix'     => 'demo:',
        'persistent' => false,
    ),

    // cookie
    'cookies'   => array(
        'prefix' => 'demo_',
        'expire' => 3600,
        'path'   => '/',
        'domain' => SDK_DOMAIN,
    ),

    // logger
    'logger' => array(
        'adapter' => 'file',
        'level' => \Phalcon\Logger::DEBUG,
        'file' => array(
            'filename' => APP_LOG . DS . APP_NAME . LOGEXT,
        ),
    )
);