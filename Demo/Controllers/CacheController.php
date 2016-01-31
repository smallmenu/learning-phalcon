<?php
/**
 * Cache
 *
 * @author
 * @copyright
 */

namespace Demo\Controllers;

use Phalcon\Mvc\Controller;

class CacheController extends Controller
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
    }

    public function indexAction()
    {
        echo 'helloworld';
        $this->view->disable();
    }

    /**
     *
     * @debug http://demo.phalcon.loc/cache/set
     */
    public function setAction()
    {
        // cache 会依据crypt自动加密
        $this->cache->save('cache', 'cache value');
        $this->cache->save('cache300', 'cache value', 300);
        $this->cache->save('cachedata', array('test1', 'test2'));

        echo 'cache set';
        $this->view->disable();
    }

    /**
     *
     * @debug http://demo.phalcon.loc/cache/get
     */
    public function getAction()
    {
        if ($this->cache->exists('cache')) {
            echo 'cache:'. $this->cache->get('cache');
            echo PHP_EOL;
            $this->cache->delete('cache');
            echo 'cache:'. $this->cache->get('cache');
            echo PHP_EOL;
        }
        $this->view->disable();
    }

    public function flushAction()
    {
        $this->cache->flush();
    }
}