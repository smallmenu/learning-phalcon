<?php
/**
 * Flash
 *
 * @author
 * @copyright
 */

namespace Demo\Controllers;

use Phalcon\Mvc\Controller;

class FlashController extends Controller
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
     * @debug http://demo.phalcon.loc/flash
     */
    public function indexAction()
    {
        $this->flash->success("The post was correctly saved!");
    }
}

