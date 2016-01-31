<?php
/**
 * Cookie
 *
 * @author niuchaoqun
 * @copyright
 */

namespace Extend\Phalcon\Http\Response;


class Cookies extends \Phalcon\Http\Response\Cookies
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param $options
     */
    public function __construct($options)
    {
        if ($options && is_array($options)) {
            $this->options = $options;
        }
    }

    /**
     * @param string $name
     * @param null $value
     * @param int $expire
     * @param string $path
     * @param null $secure
     * @param null $domain
     * @param null $httpOnly
     * @return \Phalcon\Http\Response\Cookies
     */
    public function set($name, $value = null, $expire = 0, $path = "/", $secure = null, $domain = null, $httpOnly = null)
    {
        $name = isset($this->options['prefix']) ? $this->options['prefix'].$name : $name;
        $expire = $expire === 0 ? (isset($this->options['expire']) ? time() + $this->options['expire'] : 0) : $expire;
        $path = $path === '/' ? (isset($this->options['path']) ? $this->options['path'] : '/') : $path;
        $domain = $domain === null ? (isset($this->options['domain']) ? $this->options['domain'] : null) : $domain;
        $secure = $secure === null ? (isset($this->options['secure']) ? $this->options['secure'] : null) : $secure;
        $httpOnly = $httpOnly === null ? (isset($this->options['httpOnly']) ? $this->options['httpOnly'] : null) : $httpOnly;

        return parent::set($name, $value, $expire, $path, $secure, $domain, $httpOnly);
    }

    public function get($name)
    {
        $name = isset($this->options['prefix']) ? $this->options['prefix'].$name : $name;
        return parent::get($name);
    }

    public function has($name)
    {
        $name = isset($this->options['prefix']) ? $this->options['prefix'].$name : $name;
        return parent::has($name);
    }

    public function delete($name)
    {
        $name = isset($this->options['prefix']) ? $this->options['prefix'].$name : $name;
        return parent::delete($name);
    }
}