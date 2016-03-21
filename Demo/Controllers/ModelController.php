<?php
/**
 * Model
 *
 * @author
 * @copyright
 */

namespace Demo\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Db\Column;
use Demo\Models\Item;
use Demo\Models\Member;

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
     * 根据主键查询一条记录
     *
     * @debug http://demo.phalcon.loc/model/findfirst
     */
    public function findfirstAction()
    {
        // 主键
        $item = Item::findFirst(1);

        if ($item) {
            echo 'id:' . $item->id;
            echo PHP_EOL;
            echo 'name:' . $item->name;
            echo PHP_EOL;

            // or
            print_r($item->toArray());
        }


        $item = Item::findFirst("id = 2");

        $item = Item::find();
        if (!$item->count()) {
            print_r($item->toArray());
        }
    }

    /**
     * COUNT
     *
     * @debug http://demo.phalcon.loc/model/count
     */
    public function countAction()
    {
        // 只是count取回来的数据
        $items = Item::find();
        echo "count object: ", count($items);
        echo "count object: ", $items->count();
        echo PHP_EOL;

        // 真实的COUNT用法
        $counts = Item::count();
        echo "count COUNT: ", $counts;
        echo PHP_EOL;

        $counts = Item::count("id = 1 OR id = 2");
        echo "count COUNT(condition): ", $counts;
        echo PHP_EOL;

        $counts = Item::count(array(
            "id > ?0",
            "bind" => array(2),
        ));
        echo "count COUNT(condition): ", $counts;
        echo PHP_EOL;
    }

    /**
     * 查询多条
     *
     * @debug http://demo.phalcon.loc/model/find
     */
    public function findAction()
    {
        // 默认第一个参数为查询条件
        $items = Item::find(" id = 1 ");
        console($items->toArray(), false, '1');

        // 复杂查询
        $items = Item::find(array(
            // 默认第一个参数为查询条件
            " id >= 1 ",   // 等同于 "conditions"     => " id >= 1 ",
            "columns" => 'id, name',
            "order"   => 'autocreated DESC',
            "offset"  => 2,
            "limit"   => 10,
            //'group' => 'name',
            //'for_update' => true,  读取最新的可用数据，并且为读到的每条记录设置独占锁
            //'shared_lock' => true, 读取最新的可用数据，并且为读到的每条记录设置共享锁
        ));
        console($items->toArray(), false, '2');

        // 参数绑定
        $items = Item::find(array(
            "id = ?0 OR name = ?1",
            "bind" => array(1, '魅族'),
        ));
        console($items->toArray(), false, '3');

        $items = Item::find(array(
            "conditions" => "id = :id: OR name = :name: ",
            "bind"       => array('id' => 'a', 'name' => '魅族'),
        ));
        console($items->toArray(), false, '4');

        // 更严谨的绑定
        $items = Item::find(array(
            "id = :id: OR name = :name: ",
            "bind"      => array('id' => 'a', 'name' => '魅族'),
            "bindTypes" => array('id' => Column::BIND_PARAM_INT, 'name' => Column::BIND_PARAM_STR)
        ));
        console($items->toArray(), false, '5');
    }

    /**
     *
     * @debug http://demo.phalcon.loc/model/advfind
     */
    public function advfindAction()
    {
        // IN 参数绑定
        $in = array(1, 3);
        $items = Item::find(array(
            "id IN ({in:array})",
            "bind" => array('in' => $in),
        ));
        console($items->toArray());

        // LIKE
        $items = Item::find(array(
            "name LIKE :name: ",
            "bind" => array('name' => '%Apple%'),
        ));
        console($items->toArray());
    }

    /**
     * 实际上是对查询的结果进行过滤，用于扩展
     *
     * @debug http://demo.phalcon.loc/model/filter
     */
    public function filterAction()
    {
        $item = Item::find()->filter(
            function ($item) {
                if (filter_var($item->name, FILTER_VALIDATE_URL)) {
                    return $item;
                }
            }
        );
        console($item);
    }

    /**
     *
     * @debug http://demo.phalcon.loc/model/create
     */
    public function createAction()
    {
        $member = new Member();
        $member->username = 'aaaaa';
        $member->email = 'aaaaa@phalcon.com';
        $member->password = '123456';

        if ($member->create() == false) {
            console($member->getMessages());
            foreach ($member->getMessages() as $message) {
                echo $message, "\n";
            }
        } else {
            echo 'InsertID.'. $member->id;
            echo "created successfully";
        }

        // 关联数组，需要重置 new 一个新的对象
        $member = new Member();
        $data = array(
            'username' => 'bbbbb',
            'email' => 'bbbbb@phalcon.loc',
            'password' => '123123',
        );
        if ($member->create($data) == false) {
            foreach ($member->getMessages() as $message) {
                echo $message, "\n";
            }
        } else {
            $member = $member->toArray();
            echo 'InsertID.'. $member['id'];
            echo "created successfully";
        }
    }

    /**
     *
     * @debug http://demo.phalcon.loc/model/save
     */
    public function saveAction()
    {
        $member = Member::findFirst(2);
        if ($member) {
            $member->password = '333';
            if ($member->save() == false) {
                foreach ($member->getMessages() as $message) {
                    echo $message, "\n";
                }
            } else {
                echo "save successfully";
            }
        } else {
            echo 'not exist';
        }
    }

    /**
     * Model->afterFetch作用于Model对象，期望find()生效的话需要遍历Moel对象
     *
     * @debug http://demo.phalcon.loc/model/fetch
     */
    public function fetchAction()
    {
        $member = Member::findFirst();
        console($member->toArray());

        $members = Member::find();
        foreach ($members as $key => $member) {
            console($member->toArray());
        }
    }

    /**
     *
     * @debug http://demo.phalcon.loc/model/delete
     */
    public function deleteAction()
    {
        $member = Member::findFirst("id=2 AND status = 1");
        if ($member) {
            $result = $member->delete();
            if ($result) {
                echo 'soft deleted';
            }
        } else {
            echo 'not exist';
        }
    }

    /**
     *
     * @debug http://demo.phalcon.loc/model/query
     */
    public function queryAction()
    {
        $robots = Member::query()
            ->columns('id, name')
            ->where('id >=1')
            ->order('year DESC')
            ->execute();
        print_r($robots->toArray());
        exit;
    }
}