<?php
/**
 * Cookie
 *
 * @author
 * @copyright
 */

namespace Demo\Controllers;

use Phalcon\Mvc\Controller;

class CookieController extends Controller
{

    /**
     * __construct alias
     */
    public function onConstruct() {}

    /**
     * 初始化控制器
     */
    public function initialize(){}

    /**
     *
     * @debug http://demo.phalcon.loc/cookie/set
     */
    public function setAction()
    {
        // cookie 会依据crypt自动加密
        $this->cookies->set('remember-me', 'cookies value', time()+1200);
        echo 'cookies set';
    }

    /**
     *
     * @debug http://demo.phalcon.loc/cookie/get
     */
    public function getAction()
    {
        if ($this->cookies->has('remember-me')) {

            // 获取cookie对象，echo会调用toString方法
            $rememberMe = $this->cookies->get('remember-me');
            echo $rememberMe;echo PHP_EOL;

            $this->cookies->set('remember-me', null, time()-86400);
        }
    }
}