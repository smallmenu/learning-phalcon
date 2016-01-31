<?php
/**
 * Model
 *
 * @author niuchaoqun
 * @copyright
 */

namespace Demo\Models;

use Phalcon\Mvc\Model;

class UaMessage extends Model
{
    public function initialize()
    {
        $this->setSource("ua_message");
    }
}