<?php
/**
 * Redis
 *
 * @author niuchaoqun
 * @copyright
 */

namespace Extend\Phalcon\Session\Adapter;

use Phalcon\Session\Adapter;
use Phalcon\Session\AdapterInterface;
use Phalcon\Cache\Backend\Redis as BackendRedis;
use Phalcon\Cache\Frontend\Data as FrontendData;

class Redis extends Adapter implements AdapterInterface
{
    protected $redis = null;

    protected $lifetime = 1800;

    public function __construct($options = null)
    {
        if (!is_array($options)) {
            $options = array();
        }

        if (!isset($options['host'])) {
            $options['host'] = '127.0.0.1';
        }

        if (!isset($options['port'])) {
            $options['host'] = '6379';
        }

        if (!isset($options['persistent'])) {
            $options['host'] = 0;
        }

        if (!isset($options['port'])) {
            $options['host'] = '6379';
        }

        if (isset($options['lifetime'])) {
            $this->lifetime = $options['lifetime'];
        }

        $this->redis = new BackendRedis(
            new FrontendData(array('lifetime' => $this->lifetime)),
            $options
		);

		session_set_save_handler(
            array($this, 'open'),
            array($this, 'close'),
            array($this, 'read'),
            array($this, 'write'),
            array($this, 'destroy'),
            array($this, 'gc')
        );

        parent::__construct($options);
    }

    public function open()
    {
        return true;
    }

    public function close()
    {
        return true;
    }


    public function read($session_id)
	{
        return $this->redis->get($session_id, $this->lifetime);
	}

    public function write($session_id, $data)
	{
        $this->redis->save($session_id, $data, $this->lifetime);
	}

    public function destroy($session_id = null)
    {
        if ($session_id === null) {
            $session_id = $this->getId();
		}
        return $this->redis->delete($session_id);
	}

    public function gc()
    {
        return true;
    }
}