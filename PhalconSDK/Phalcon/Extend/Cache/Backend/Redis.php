<?php
namespace Phalcon\Extend\Cache\Backend;

class Redis extends \Phalcon\Cache\Backend\Redis
{
    public function __construct(\Phalcon\Cache\FrontendInterface $frontend, $options = null)
    {
        parent::__construct($frontend, $options);
    }

    public function __call($method, array $arguments)
    {
        if (!is_object($this->_redis)) {
            $this->_connect();
        }
        if (method_exists($this->_redis, $method)) {
            if (!empty($arguments) && count($arguments) > 1) {
                $option = $this->_options;
                $keyName = $option['statsKey']. $option['prefix']. $arguments[0];
                $arguments[0] = $keyName;
            }
            return call_user_func_array(array($this->_redis, $method), $arguments);
        } else {
            throw new \Phalcon\Cache\Exception('method ' . $method . ' not exists');
        }
    }
}