<?php

namespace Demo\Controllers;

use Demo\Models\UaMessage;
use Phalcon\Mvc\Controller;

class TestController extends Controller
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

    /**
     *
     * @debug http://demo.phalcon.loc/test
     */
    public function indexAction()
    {

        $i = 1;
        $i = 2;
        $i = 12;
        echo pack('N', $i);
    }


    /**
     *
     * @debug http://demo.phalcon.loc/test/test
     */
    public function testAction()
    {
        $ua = UaMessage::find(array(
            // 默认第一个参数为查询条件
            " id >= 1 ",
            // 等同于
            "conditions"     => " id >= 1 ",
            "limit"          => 4,
        ));
        print_r($ua->toArray());
        exit;
    }

    /**
     *
     * @debug http://demo.phalcon.loc/test/json
     */
    public function jsonAction()
    {
        $json1 = array(1, 2, 3);
        $json2 = array('1', '2', '3');
        $json3 = array('k1'=>'中文', 'k2'=>'v2');
        $json4 = array(
            array(1,2,3),
            array('k1'=>'v1', 'k2'=>'v2'),
            array('k1'=>'v1', 'k2'=>'v2'),
        );
        $json5 = array(
            'code' => 0,
            'data1' => array(
                'key' => '中文',
            ),
            'data2' => array(
                array('s' => 'string1', 'int'=>1, 'bool'=> true, 'n' => null),
                array('s' => 'string2', 'int'=>2, 'bool'=> false, 'n' => ''),
            ),
            'msg' => 'ok',
        );
        $json6 = array();
        $json7 = new \stdClass();

        echo json_encode($json1);
        echo PHP_EOL;
        echo json_encode($json2);
        echo PHP_EOL;
        echo json_encode($json3);
        echo PHP_EOL;
        echo json_encode($json4);
        echo PHP_EOL;
        echo json_encode($json5);
        echo PHP_EOL;
        echo json_encode($json6);
        echo PHP_EOL;
        echo json_encode($json7);
        echo PHP_EOL;
        print_r(json_decode(json_encode($json5), true));
    }
}