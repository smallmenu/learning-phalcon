<?php
/**
 * Logger
 *
 * @author
 * @copyright
 */

namespace Demo\Controllers;

use Phalcon\Mvc\Controller;

class LoggerController extends Controller
{
    /**
     * __construct alias
     */
    public function onConstruct()
    {
    }

    /**
     * 初始化控制器
     */
    public function initialize()
    {
        $this->view->disable();
    }

    /**
     *
     * @debug http://demo.phalcon.loc/logger/
     */
    public function indexAction()
    {
        /** @var \Phalcon\Logger\Adapter $logger */
        $logger = $this->logger;
        $logger->debug("This is a debug message");
        $logger->info("This is an info message");
        $logger->notice("This is a notice message");
        $logger->warning("This is a warning message");
        $logger->error("This is an error message");
    }
}