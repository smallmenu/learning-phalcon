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
        $this->cookies->set('remember-me', 'some value', time() + 36000);
        echo 'cookie set';
    }

    public function getAction()
    {
        if ($this->cookies->has('remember-me')) {

            // 获取cookie对象，echo会调用toString方法
            $rememberMe = $this->cookies->get('remember-me');
            echo $rememberMe;echo PHP_EOL;

            // 我认为是phalcon的Bug，在指定domain的情况不能直接使用delete方法删除
            $this->cookies->set('remember-me', null, time()-3600);
            //$this->cookies->delete('remember-me');
        }
    }
}