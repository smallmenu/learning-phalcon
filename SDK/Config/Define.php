<?php
// 运行模式 Develop 或 Develop, Product
define('RUNTIME_MODE', 'Develop');
// 运行时间
define('RUNTIME_START_TIME', microtime(true));
// 调试模式
define('DEBUG_MODE', true);

// SDK MySQL
define('SDK_MYSQL_HOST', '192.168.0.117');
define('SDK_MYSQL_PORT', 3307);
define('SDK_MYSQL_USER', 'dahaiyang');
define('SDK_MYSQL_PASS', 'dahaiyang2016');

// SDK Redis
define('SDK_REDIS_HOST', '127.0.0.1');
define('SDK_REDIS_PORT', 6379);
define('SDK_REDIS_INDEX', 1);
define('SDK_REDIS_AUTH', '123123');

// SDK Memcache
define('SDK_MEMCACHE_HOST', '127.0.0.1');
define('SDK_MEMCACHE_PORT', 11211);

// Define
define('SDK_CONFIG', SDK_DIR. '/Config');
define('DS', DIRECTORY_SEPARATOR);
define('PHPEXT', '.php');
define('LOGEXT', '.log');