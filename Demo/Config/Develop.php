<?php
/**
 *
 */
return array(
    // app
    'app'     => array(
        'debug'    => true,
        'loglevel' => \Phalcon\Logger::DEBUG,
        'domain'   => 'demo.phalcon.loc',
        'url'      => 'http://demo.phalcon.loc/',
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
    'crypt'   => array(
        'authkey' => '123',
    ),

    // database
    'db'      => array(
        'host'     => SDK_MYSQL_HOST,
        'username' => SDK_MYSQL_USER,
        'password' => SDK_MYSQL_PASS,
        'port'     => SDK_MYSQL_PORT,
        'dbname'   => 'phalcondemo',
        'charset'  => 'utf8',
    ),

    // cache
    'cache'   => array(
        'adapter'  => 'redis',
        'redis'    => array(
            'host'       => SDK_REDIS_HOST,
            'port'       => SDK_REDIS_PORT,
            'auth'       => SDK_REDIS_AUTH,
            'index'      => SDK_REDIS_INDEX,
            'prefix'     => 'Demo:',
            'persistent' => false,
        ),
        'lifetime' => 3600,
    ),

    // metadata
    'metadata'   => array(
        'adapter'  => 'redis',
        'redis'    => array(
            'host'       => SDK_REDIS_HOST,
            'port'       => SDK_REDIS_PORT,
            'auth'       => SDK_REDIS_AUTH,
            'index'      => SDK_REDIS_INDEX,
            'prefix'     => 'DemoMetaData:',
            'persistent' => false,
            'lifetime' => 600,
        ),
    ),

    // session
    'session' => array(
        'adapter' => 'redis',
        'options' => array(
            'cache_limiter'   => 'nocache',
            'cookie_lifetime' => 600,
            'cookie_path'     => '/',
            'cookie_domain'   => 'phalcon.loc',
            'cookie_httponly' => true,
        ),
        'redis'   => array(
            'host'       => SDK_REDIS_HOST,
            'port'       => SDK_REDIS_PORT,
            'auth'       => SDK_REDIS_AUTH,
            'index'      => SDK_REDIS_INDEX,
            'prefix'     => 'Demo:',
            'lifetime'   => 600,
            'persistent' => false,
        ),
    ),

    // redis
    'redis'   => array(
        'host'       => SDK_REDIS_HOST,
        'port'       => SDK_REDIS_PORT,
        'auth'       => SDK_REDIS_AUTH,
        'index'      => SDK_REDIS_INDEX,
        'prefix'     => 'Demo:',
        'persistent' => false,
    ),

    // cookie
    'cookies' => array(
        'prefix' => 'demo_',
        'expire' => 3600,
        'path'   => '/',
        'domain' => 'phalcon.loc',
    ),

    // logger
    'logger'  => array(
        'adapter' => 'file',
        'file'    => array(
            'filename' => APP_LOG . DS . APP_NAME . LOGEXT,
        ),
    ),
);