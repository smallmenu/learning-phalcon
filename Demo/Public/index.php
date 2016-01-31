<?php
/**
 * 入口文件
 *
 * @author
 * @copyright
 */

namespace Demo;

define('APP_NAME',      'Demo');
define('APP_ENV',       'develop');
define('APP_PATH',      realpath('..') . '/');
define('APP_CONFIG',    APP_PATH. '/Config/');
define('APP_BEGIN',     microtime(true));

try {
    include APP_PATH . 'Bootstrap.php';
    $application = new \Demo\Bootstrap(APP_CONFIG. APP_ENV. '.ini');
    $application->run();
} catch (\Exception $e) {
    echo $e->getMessage();
}