<?php
/**
 *
 *
 * @author
 * @copyright
 */

namespace Demo\Models;

use Phalcon\Mvc\Model;

class ItemAlias extends Model
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
        $this->setSource("item");
    }
}