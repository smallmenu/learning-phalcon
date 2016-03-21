<?php
/**
 * Config
 *
 * @author
 * @copyright
 */

namespace Demo\Controllers;

use Phalcon\Mvc\Controller;

class ConfigController extends Controller
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
     * @debug http://demo.phalcon.loc/config/get
     */
    public function getAction()
    {
        print_r(load('Product'));
    }

    /**
     *
     * @debug http://demo.phalcon.loc/config/getsdk
     */
    public function getSDKAction()
    {
        print_r(load('Mail1', SDK_CONFIG));
    }
}