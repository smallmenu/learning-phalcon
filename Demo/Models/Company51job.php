<?php
/**
 * Model
 *
 * @author niuchaoqun
 * @copyright
 */

namespace Demo\Models;

use Phalcon\Mvc\Model;

class Company51job extends Model
{
    public function initialize()
    {
        $this->setSource("company_51job");
    }

    public function onConstruct()
    {
        $this->setSource("company_51job");
    }
}