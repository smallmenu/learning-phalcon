<?php
/**
 * Session
 *
 * @author
 * @copyright
 */

namespace Demo\Controllers;

use Phalcon\Mvc\Controller;

class SessionController extends Controller
{

    /**
     * __construct alias
     */
    public function onConstruct() {}

    /**
     * 初始化控制器
     */
    public function initialize() {}

    /**
     *
     * @debug http://demo.phalcon.loc/session/set
     */
    public function setAction()
    {
        $session1 = $this->session;
        $session2 = $this->session;

        $session1->set('session1', 'session value 1');
        $session2->set('session2', 'session value 2');
        echo 'session set';
        exit;
    }

    /**
     *
     * @debug http://demo.phalcon.loc/session/get
     */
    public function getAction()
    {
        if ($this->session->has('session1') && $this->session->has('session2')) {
            echo 'session1:';
            print_r($this->session->get('session1'));
            echo PHP_EOL;
            echo 'session2:';
            print_r($this->session->get('session2'));
        }
    }

    /**
     *
     * @debug http://demo.phalcon.loc/session/remove
     */
    public function removeAction()
    {
        $this->session->remove('session1');
        echo 'session1:';
        print_r($this->session->get('session1'));
        echo PHP_EOL;
        echo 'session2:';
        print_r($this->session->get('session2'));
        $this->session->destroy();
    }
}