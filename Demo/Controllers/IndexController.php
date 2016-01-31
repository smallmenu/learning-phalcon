<?php
/**
 * 默认控制器
 *
 * @author
 * @copyright
 */

namespace Demo\Controllers;

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    /**
     * @var
     */
    public $hello;

    /**
     * __construct alias
     */
    public function onConstruct() {}

    /**
     * 初始化控制器
     */
    public function initialize()
    {
        $this->hello = 'Hello World Phalcon!';
    }

    /**
     * 默认 Action
     *
     * @debug http://demo.phalcon.loc/
     */
    public function indexAction()
    {
        echo $this->hello;
    }

    /**
     * Params
     *
     * @param $param1
     * @param $param2
     * @debug http://demo.phalcon.loc/index/param/paramvalue1/paramvalue2?args=value1&args2=value2
     */
    public function paramAction($param1, $param2)
    {
        $params = array($param1, $param2);
        print_r($params);
        echo PHP_EOL;

        $gets = $this->request->get();
        print_r($gets);
        echo PHP_EOL;
    }

    /**
     * Froward
     *
     * @debug http://demo.phalcon.loc/index/forward
     */
    public function forwardAction()
    {
        $this->dispatcher->forward(array(
            'controller' => 'index',
            'action' => 'index',
        ));
    }

    /**
     * Get
     *
     * @debug http://demo.phalcon.loc/index/get?args1=value1&args2=value2
     */
    public function getAction()
    {
        if ($this->request->isGet()) {
            $gets = $this->request->get();
            print_r($gets);
            echo PHP_EOL;

            $agrs1 = $this->request->get('args1');
            $agrs2 = $this->request->get('args2');
            print_r("args1:" . $agrs1);
            echo PHP_EOL;
            print_r("args2:" . $agrs2);
            echo PHP_EOL;
        }

        // 关闭视图
        $this->view->disable();
    }

    /**
     * POST
     *
     * @debug http://demo.phalcon.loc/index/post
     */
    public function postAction()
    {
        if ($this->request->isPost()) {
            $posts = $this->request->getPost();
            print_r($posts); echo PHP_EOL;

            $post1 = $this->request->getPost('post1');
            $post2 = $this->request->getPost('post2');
            print_r("post1:". $post1);
            echo PHP_EOL;
            print_r("post2:". $post2);
            echo PHP_EOL;

            // 关闭视图
            $this->view->disable();
        }
    }

    /**
     * 视图
     *
     * @debug http://demo.phalcon.loc/index/view
     */
    public function viewAction()
    {
        $title = 'Title';

        $this->view->setVar('title', $title);
        $this->view->setVars(array(
            'var1' => 'value1',
            'var2' => 'value2'
        ));

        $this->view->content = 'This is Content';

        // 重定义视图
        $this->view->pick('index/view-other');
    }

    /**
     * 404
     *
     * @debug http://demo.phalcon.loc/index/notfound
     */
    public function notfoundAction()
    {
        $this->response->setStatusCode(404, "Not Found");
    }
}