<?php
/**
 * 入口
 *
 * @author
 * @copyright
 */

use Demo\Bootstrap;
use Phalcon\Http\Response;
use Phalcon\DI\FactoryDefault;

try {
    include __DIR__ . '/../Config/Define.php';
    include SDK_DIR . '/SDK.php';
    include APP_DIR . '/Bootstrap.php';

    $application = new Bootstrap();
    $application->run();
} catch (\Phalcon\Mvc\Dispatcher\Exception $e) {
    FactoryDefault::getDefault()->get('logger')->notice($e->getMessage());
    $response = new Response();
    $response->setStatusCode(404, 'Not Found')->send();
} catch (\Exception $e) {
    $message = 'Exception: ' . $e->getMessage();
    FactoryDefault::getDefault()->get('logger')->error($message);
    $response = new Response();
    $response->setStatusCode(503, debugMode() ? $message : null)->send();
}