<?php
/**
 *
 */
return array(
    // app
    'app'       => array(
        'debug'    => true,
        'loglevel' => \Phalcon\Logger::DEBUG,
        'domain'   => 'test.phalcon.loc',
        'url'      => 'http://test.phalcon.loc/',
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
        'port'     => SDK_MYSQL_PORT,
        'dbname'   => 'solr',
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
            'prefix'     => 'Test:',
            'persistent' => false,
        ),
        'lifetime' => 3600,
    ),

    // metadata
//    'metadata'   => array(
//        'adapter'  => 'redis',
//        'redis'    => array(
//            'host'       => SDK_REDIS_HOST,
//            'port'       => SDK_REDIS_PORT,
//            'auth'       => SDK_REDIS_AUTH,
//            'index'      => SDK_REDIS_INDEX,
//            'prefix'     => 'TestMetaData:',
//            'persistent' => false,
//            'lifetime' => 10,
//        ),
//    ),

    // session
    'session'   => array(
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
            'prefix'     => 'Test:',
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
        'prefix'     => 'Test:',
        'persistent' => false,
    ),

    // cookie
    'cookies'   => array(
        'prefix' => 'test_',
        'expire' => 3600,
        'path'   => '/',
        'domain' => 'phalcon.loc',
    ),
);