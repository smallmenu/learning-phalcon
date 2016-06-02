<?php
/**
 * SC
 *
 * @author
 * @copyright
 */

namespace Test\Models;

use Phalcon\Mvc\Model;

class Sc extends Model
{
    /**
     * 构造函数Alias
     */
    public function onConstruct()
    {

    }

    /**
     * 请求期间仅会被调用一次
     */
    public function initialize()
    {
    }
}