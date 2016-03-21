<?php
/**
 * CLI 入口
 *
 * @author
 * @copyright
 */

use Test\Bootstrap;
use Phalcon\Http\Response;
use Phalcon\DI\FactoryDefault;

set_time_limit(0);

try {
    include __DIR__ . '/../Config/Define.php';
    include SDK_DIR . '/SDK.php';
    include APP_DIR . '/Bootstrap.php';


    $application = new Bootstrap('CLI');
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
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage(). PHP_EOL;
    exit(-1);
}