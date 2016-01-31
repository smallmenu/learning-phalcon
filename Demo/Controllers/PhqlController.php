<?php
/**
 * Phql
 *
 * @author
 * @copyright
 */

namespace Demo\Controllers;

use Phalcon\Mvc\Controller;

class PhqlController extends Controller
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
     * @debug http://demo.phalcon.loc/phql/execute
     */
    public function executeAction()
    {
        $query  = $this->modelsManager->createQuery("SELECT * FROM Demo\Models\Robots");
        $robots   = $query->execute();

        $query  = $this->modelsManager->createQuery("SELECT * FROM Demo\Models\Robots WHERE name = :name:");
        $robots   = $query->execute(array(
            'name' => '张三'
        ));

        // PHQL会自动优化*为表字段
        $phql = "SELECT r.* FROM Demo\Models\Robots AS r ORDER BY r.name";
        $robots = $this->modelsManager->executeQuery($phql);

        $phql = "SELECT r.*, rp.* FROM Demo\Models\Robots AS r,  Demo\Models\RobotsParts rp WHERE r.id = rp.robots_id";
        $rows = $this->modelsManager->executeQuery($phql);
        print_r($rows->toArray());
        exit;
    }
}