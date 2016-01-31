<?php
/**
 * 入口文件
 *
 * @author
 * @copyright
 */

define('APP_NAME',      'Demo');
define('APP_ENV',       'develop');
define('APP_PATH',      realpath('..') . '/');
define('APP_CONFIG',    APP_PATH. '/Config/');
define('APP_BEGIN',     microtime(true));


try {
    set_time_limit(0);

    include APP_PATH . 'Bootstrap.php';
    $application = new \Demo\Bootstrap(APP_CONFIG. APP_ENV. '.ini', 'CLI');

    $arguments = array();
    foreach ($argv as $k => $arg) {
        if ($k == 1) {
            $arguments['task'] = $arg;
        } elseif ($k == 2) {
            $arguments['action'] = $arg;
        } elseif ($k >= 3) {
            $arguments['params'][] = $arg;
        }
    }

    define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
    define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

    $application->run($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    var_dump($e->getTraceAsString());
    exit(255);
}