<?php
/**
 * City
 *
 * @author
 * @copyright
 */

namespace Test\Controllers;

use Phalcon\Mvc\Controller;

class IndexController extends Controller
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

    public function indexAction()
    {
        echo 'test';
    }
}