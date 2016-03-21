<?php
/**
 *
 *
 * @author
 * @copyright
 */

namespace Phalcon\Extend\Session\Adapter;

class Redis extends \Phalcon\Session\Adapter\Redis
{
    /**
     * 更严谨的Destroy Rediss Session
     *
     * @param null $session_id
     * @return mixed
     */
    public function destroy($session_id = null)
    {
        if ($session_id === null) {
            $session_id = $this->getId();
        }
        $_SESSION = null;
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        return $this->_redis->delete($session_id);
    }
}