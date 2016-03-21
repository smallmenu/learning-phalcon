<?php
// 全局配置
include __DIR__ . '/Config/Define.php';

$loader = new \Phalcon\Loader();
$loader->registerNamespaces(array(
    'SDK'     => __DIR__,
    'Phalcon' => __DIR__ . '/Phalcon',
))->register();

/**
 * 获取应用主配置或模块配置
 *
 * <code>
 *
 * config('database'); // 获取应用database组配置
 * config('database.host'); // 获取应用database组配置host键
 *
 *</code>
 *
 * @param $key
 * @param null $default
 * @return array|mixed|null
 * @throws \Phalcon\Config\Exception
 */
function config($key, $default = null)
{
    static $caches = array();

    if (isset($caches[$key])) {
        return $caches[$key];
    }

    // fetch subkey
    $parts = explode('.', $key);
    $group = array_shift($parts);
    if (empty($group)) {
        return $caches[$key] = $default;
    }

    // override runtime config
    $runtimes = explode(',', RUNTIME_MODE);
    foreach ($runtimes as $k => &$v) {
        $v = trim($v);
        if (empty($v)) {
            unset($runtimes[$k]);
        }
    }
    if (isset($runtimes[1])) {
        $file = $runtimes[0];
        $override_file = $runtimes[1];
        $config = load($file);
        $override_config = load($override_file);
        $config = array_merge($config, $override_config);
    } else {
        $file = $runtimes[0];
        $config = load($file);
    }

    if (empty($config)) {
        return $caches[$key] = $default;
    }

    $ret = $config;
    $parts = explode('.', $key);
    while ($group = array_shift($parts)) {
        if (isset($ret[$group])) {
            $ret = $ret[$group];
        } else {
            return $caches[$key] = $default;
        }
    }

    return $caches[$key] = $ret;
}

/**
 * 获取指定路径的配置文件，并返回对象，默认获取应用配置路径
 *
 * <code>
 * load('router'); // 获取应用路由配置
 * load('mail', SDK_CONFIG); // 获取SDK配置
 * </code>
 *
 * @param $name
 * @return mixed
 * @return mixed
 * @throws \Phalcon\Config\Exception
 */
function load($name, $dir = null, $default = null)
{
    static $caches = array();

    if (isset($caches[$name])) {
        return $caches[$name];
    }

    $name = ucfirst($name);
    $dir = $dir === null ? APP_CONFIG : $dir;
    $filepath = $dir . DS . $name . PHPEXT;
    if (is_file($filepath) && is_readable($filepath)) {
        $load = require $filepath;
    } elseif ($default) {
        $load = $default;
    } else {
        throw new \Phalcon\Config\Exception('Failed to require config file');
    }

    return $caches[$name] = $load;
}

/**
 * 为浏览器的 FirePHP / ChromePHP 扩展输出响应头数据，以便调试
 *
 * <code>
 * console($data);
 * console($data, true);
 * </code>
 *
 * @param $data
 * @param $time
 */
function console($data, $time = false, $label = null)
{
    static $logger;
    static $index = 0;
    static $lasttime = RUNTIME_START_TIME;

    $thistime = microtime(true);
    $usedtime = $thistime - $lasttime;
    $lasttime = $thistime;
    $label = $time ? sprintf("%09.5fs", $usedtime) : $label;

    if (is_null($logger)) {
        if (strstr($_SERVER['HTTP_USER_AGENT'], ' Firefox/')) {
            $logger = new \SDK\Library\Util\FirePHP\FirePHP();
        } elseif (strstr($_SERVER['HTTP_USER_AGENT'], ' Chrome/')) {
            $logger = \SDK\Library\Util\ChromePHP\ChromePHP::getInstance();
        } else {
            $logger = false;
        }
    }

    if ($logger) {
        if ($logger instanceof \SDK\Library\Util\FirePHP\FirePHP) {
            $logger->info($data, $label);
        } else if ($logger instanceof \SDK\Library\Util\ChromePHP\ChromePHP) {
            if ($label) {
                $logger->info($label, $data);
            } else {
                $logger->info($data);
            }
        }
    } else {
        $name = 'Console-' . ($index++);
        if ($label) {
            $name .= '#' . $label;
        }
        header($name . ':' . json_encode($data));
    }
}

/**
 * 判断全局与应用调试模式
 *
 * @return bool
 */
function debugMode()
{
    $appdebug = config('app.debug');
    if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
        return true;
    } else {
        if ($appdebug) {
            return true;
        }
    }
    return false;
}

/**
 * 获取数组中的值，不存在时返回默认值
 *
 * @param $array
 * @param $key
 * @param $default
 * @param bool $allow_empty
 * @return mixed
 */
function val($array, $key, $default = null, $allow_empty = true)
{
    $ret = $default;
    if (is_array($array) && array_key_exists($key, $array)) {
        $ret = $array[$key];
        if (empty($ret) && !$allow_empty) {
            $ret = $default;
        }
    }

    return $ret;
}

/**
 * 编号加密
 *
 * @param $id
 * @return string
 */
function idEncrypt($id)
{
    $pad = substr($id, -1, 1);
    $id = str_pad($id, 9, '0', STR_PAD_LEFT);
    $id = strtr(base64_encode($id), '+/', '-_');
    $id = substr($id, 0, 9) . $pad . substr($id, 9);

    return $id;
}

/**
 * 编号解密
 *
 * @param $id
 * @return int
 */
function idDecrypt($id)
{
    if (strlen($id) < 12) return $id;
    $id = substr($id, 0, 9) . substr($id, 10);
    $id = base64_decode(strtr($id, '-_', '+/'));

    return (int)$id;
}

/**
 * 截取指定字符串到一定的长度
 *
 * @param $string
 * @param $length
 * @param string $dot
 * @return mixed|string
 */
function strNatcut($string, $length, $dot = '...')
{
    if (!$string || !$length) {
        return $string;
    }

    $string = htmlspecialchars_decode($string);
    $string = preg_replace('/\s{2,}/', ' ', $string);
    $maxlen = min($length, mb_strlen($string, 'UTF-8'));
    $chars = preg_split(
        '/([\xF0-\xF7][\x80-\xBF]{3}|[\xE0-\xEF][\x80-\xBF]{2}|[\xC0-\xDF][\x80-\xBF]|[\x00-\x7F])/m',
        $string,
        $maxlen + 1,
        PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
    );
    $index = 0;
    $length = .0;
    $total = count($chars);
    $result = array();
    while ($length < $maxlen && $index < $total) {
        $char = $chars[$index];
        if (preg_match('/[\xE0-\xEF][\x80-\xBF]{2}/', $char)) {
            if (($maxlen - $length) < 1) break;
            $length += 1;
        } else {
            $length += 0.5;
        }
        $result[] = $char;
        $index++;
    }
    $result = implode('', $result);
    unset($chars, $char);

    return htmlspecialchars($result) . ($result == $string ? '' : $dot);
}

/**
 * 截取指定字符串到一定的长度，每个字符算一个长度
 *
 * @param $string
 * @param $length
 * @param null $suffix
 * @return null|string
 */
function strCharcut($string, $length, $suffix = null)
{
    $length = intval($length);

    if (!$string || !$length) {
        return $string;
    }

    $regex = '/(?<!^)(?!$)/u';

    if ($suffix === null) {
        $suffix_array = array();
        $suffix_length = 0;
    } else {
        $suffix_array = preg_split($regex, $suffix);
        $suffix_length = count($suffix_array);
    }

    $length -= $suffix_length;
    if ($length < 1) {
        return null;
    }

    $result_array = preg_split($regex, $string, $length + 1);
    array_splice($result_array, $length);
    $result_array = array_merge($result_array, $suffix_array);

    return implode('', $result_array);
}