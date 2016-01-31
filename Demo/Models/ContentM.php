<?php
/**
 * Model
 *
 * @author niuchaoqun
 * @copyright
 */

namespace Demo\Models;

use Phalcon\Mvc\Model;

class ContentM extends Model
{
    public function initialize()
    {
        $this->setSource("content_m");
    }

    public function onConstruct()
    {
        $this->setSource("content_m");
    }
}