<?php
/**
 * Model
 *
 * @author
 * @copyright
 */

namespace Demo\Controllers;

use Phalcon\Mvc\Controller;
use Demo\Models\Robots;

class ModelController extends Controller
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
     * @debug http://demo.phalcon.loc/model/findfirst
     */
    public function findfirstAction()
    {
        // 主键
        $robot = Robots::findFirst(1);

        echo 'id:'. $robot->id; echo PHP_EOL;
        echo 'name:'. $robot->name;  echo PHP_EOL;
    }

    /**
     *
     * @debug http://demo.phalcon.loc/model/find
     */
    public function findAction()
    {
        // 默认第一个参数为查询条件
        $robots = Robots::find(" year = 2013 ");
        $robot = Robots::findFirst(" name = '张三' ");

        // 复杂查询
        $robots = Robots::find(array(
            // 默认第一个参数为查询条件
            " id >= 1 ",
            // 等同于
            "conditions"     => " id >= 1 ",
            "columns"        => 'id, name, year',
            "order"          => 'year DESC',
            "limit"          => 4,
        ));
        //print_r($robots->toArray());exit;

        // 参数绑定
        $robots = Robots::find(array(
            "conditions" => "year = ?1",
            "bind"       => array(1 => 2013)
        ));
        $robots = Robots::find(array(
            "conditions" => "name = :name: AND year = :year: ",
            "bind"       => array('name'=> '张三', 'year' => 2015)
        ));
        print_r($robots->toArray());exit;
    }

    /**
     *
     * @debug http://demo.phalcon.loc/model/query
     */
    public function queryAction()
    {
        $robots = Robots::query()
            ->columns('id, name')
            ->where('id >=1')
            ->order('year DESC')
            ->execute();
        print_r($robots->toArray());
        exit;
    }

    /**
     *
     * @debug http://demo.phalcon.loc/model/findfirst
     */
    public function createAction()
    {
        $robot       = new Robots();
        $robot->type = "mechanical";
        $robot->name = "Astro Boy";
        $robot->year = 1952;

        if ($robot->create() == false) {
            foreach ($robot->getMessages() as $message) {
                echo $message, "\n";
            }
        } else {
            echo "Great, a new robot was created successfully!";
        }


    }
}