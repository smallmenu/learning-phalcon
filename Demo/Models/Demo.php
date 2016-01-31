<?php
/**
 * Model
 *
 * @author niuchaoqun
 * @copyright
 */

namespace Demo\Models;

use Phalcon\Mvc\Model;

class Demo extends Model
{
    public function initialize()
    {
        //$this->setSource("the_robots");
    }

    public function onConstruct()
    {
        //$this->setSource("the_robots");
    }
}