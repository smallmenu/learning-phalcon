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
        $this->view->disable();
    }

    /**
     *
     * @debug http://demo.phalcon.loc/cache/set
     */
    public function setAction()
    {
        /** @var \Phalcon\Cache\BackendInterface $cache */
        $cache = $this->cache;

        $cache->save('cache', 'cache', -1);
        $cache->save('cachedefault', 'cache default');
        $cache->save('cache300', 'cache 300', 300);
        $cache->save('cachedata', array('test1', 'test2'));

        echo 'cache set';
        $this->view->disable();
    }

    /**
     *
     * @debug http://demo.phalcon.loc/cache/get
     */
    public function getAction()
    {
        /** @var \Phalcon\Cache\BackendInterface $cache */
        $cache = $this->cache;

        if ($cache->exists('cachedefault')) {
            echo 'cachedefault:' . $cache->get('cachedefault');
            echo PHP_EOL;
            $cache->delete('cachedefault');
            echo 'afterdelete cachedefault:' . $cache->get('cachedefault');
            echo PHP_EOL;
        }
    }

    /**
     *
     * @debug http://demo.phalcon.loc/cache/delete
     */
    public function deleteAction()
    {
        /** @var \Phalcon\Cache\BackendInterface $cache */
        $cache = $this->cache;

        $cache->delete('9sescg5v3lq93odfhd3f34aue3');
    }

    /**
     *
     * @debug http://demo.phalcon.loc/cache/keys
     */
    public function keysAction()
    {
        /** @var \Phalcon\Cache\BackendInterface $cache */
        $cache = $this->cache;

        print_r($cache->queryKeys());
    }

    /**
     *
     * @debug http://demo.phalcon.loc/cache/sadd
     */
    public function saddAction()
    {
        /** @var \Phalcon\Cache\BackendInterface $cache */
        $cache = $this->cache;

        // Just For Redis Test
        var_dump($cache->ping());
        var_dump($cache->sAdd('list', 'list2'));
    }

    /**
     *
     * @debug http://demo.phalcon.loc/cache/flushdb
     */
    public function flushDBAction()
    {
        /** @var \Phalcon\Cache\BackendInterface $cache */
        $cache = $this->cache;

        // Just For Redis Test
        var_dump($cache->flushDB());
    }

    /**
     *
     * @debug http://demo.phalcon.loc/cache/flush
     */
    public function flushAction()
    {
        /** @var \Phalcon\Cache\BackendInterface $cache */
        $cache = $this->cache;

        $cache->flush();
    }
}