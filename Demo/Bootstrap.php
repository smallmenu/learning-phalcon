<?php
/**
 * Bootstrap
 *
 * @author
 * @copyright
 */

namespace Demo;

use Phalcon\Loader;
use Phalcon\Crypt;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\CLI\Console;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Config\Adapter\Ini;
use Phalcon\Http\Response\Cookies;
use Phalcon\Cache\Frontend\Data;
use Phalcon\Cache\Backend\Memcache;
use Phalcon\Cache\Backend\Redis;
use Phalcon\Logger;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as LoggerFile;
use Phalcon\Session\Adapter\Files as SessionFiles;
use Phalcon\Session\Adapter\Memcache as SessionMemcache;
use Extend\Db\Hbase\Hbase;
use Extend\Phalcon\Session\Adapter\Redis as SessionRedis;
use Extend\Phalcon\Http\Response\Cookies as GeneralCookies;

/**
 * Class Bootstrap
 *
 */
class Bootstrap
{
    /**
     * 应用配置
     *
     * @var Ini
     */
    protected $configs;

    /**
     * 应用实例
     *
     * @var Application
     */
    protected $application;

    /**
     * CLI or Not
     *
     * @var string
     */
    protected $mode;

    /**
     * 构造函数
     *
     * @param $config
     * @param $mode
     * @throws \Exception
     */
    public function __construct($config, $mode = '')
    {
        if (!is_file($config)) {
            throw new \Exception('APP Config Valid');
        }
        $this->configs = new Ini($config);
        $this->mode = $mode;

        $this->application = $this->mode === 'CLI' ? new Console() : new Application();
    }

    /**
     * 应用目录注册
     */
    protected function autoLoader()
    {
        $loader = new Loader();
        $loader->registerNamespaces(array(
            'Demo\Controllers' => APP_PATH . $this->configs->dirs->controllersDir,
            'Demo\Models' => APP_PATH . $this->configs->dirs->modelsDir,
            'Demo\Plugins' => APP_PATH . $this->configs->dirs->pluginsDir,
            'Demo\Tasks' => APP_PATH . $this->configs->dirs->tasksDir,
            'Extend' => APP_PATH . $this->configs->dirs->extendDir,
        ))->register();
    }

    /**
     * 默认服务注入依赖
     */
    protected function services()
    {
        $configs = $this->configs;
        $mode = $this->mode;

        $di = $this->mode === 'CLI' ? new Cli() : new FactoryDefault();

        // 命名空间
        $di->set('dispatcher', function () use ($mode) {
            $dispatcher = new Dispatcher();
            $dispatcher = $mode === 'CLI' ? new \Phalcon\CLI\Dispatcher() : new Dispatcher();
            $dispatcher->setDefaultNamespace($mode === 'CLI' ? 'Demo\Tasks' : 'Demo\Controllers');
            return $dispatcher;
        });

        // 视图
        $di->set('view', function () use ($configs) {
            $view = new View();
            $view->setViewsDir(APP_PATH . $configs->dirs->viewsDir);
            return $view;
        });

        // 加解密
        $di->set('crypt', function () use ($configs) {
            $crypt = new Crypt();
            $crypt->setKey($configs->crypt->authkey);
            return $crypt;
        });

        // Cache
        $di->setShared('cache', function () use ($configs) {
            if (isset($configs->cache)) {
                $adapter = strtolower($configs->cache->adapter);
                $options = $configs->cache->toArray();
            }
            $frontend = new Data(array('lifetime' => $configs->cache->lifetime));
            switch ($adapter) {
                case 'memcache' :
                    $cache = new Memcache($frontend, $options);
                    break;
                case 'redis':
                    $cache = new Redis($frontend, $options);
                    break;
            }
            return $cache;
        });

        // Cookie
        $di->setShared('cookies', function () use ($configs) {
            if (isset($configs->cookie)) {
                $cookies = new GeneralCookies($configs->cookie->toArray());
            }
            $cookies = new Cookies();
            return $cookies;
        });

        // Session
        $di->setShared('session', function () use ($configs) {
            if (isset($configs->session)) {
                $adapter = strtolower($configs->session->adapter);
                $options = $configs->session->toArray();
            }
            switch ($adapter) {
                case 'memcache' :
                    $session = new SessionMemcache($options);
                    $session->start();
                    break;
                case 'redis' :
                    $session = new SessionRedis($options);
                    $session->start();
                    break;
                default :
                    $session = new SessionFiles();
                    $session->start();
                    break;
            }
            return $session;
        });

        // Db
        $di->setShared('db', function () use ($configs) {

            $dbclass = 'Phalcon\Db\Adapter\Pdo\\' . ucfirst($configs->database->adapter);
            $db = new $dbclass($configs->database->toArray());

            if ($configs->application->debug) {
                $eventsManager = new EventsManager();
                $logger = new LoggerFile(APP_PATH . $configs->dirs->logDir . 'db.log');
                $eventsManager->attach('db', function ($event, $db) use ($logger) {
                    if ($event->getType() == 'beforeQuery') {
                        $logger->log($db->getSQLStatement(), Logger::INFO);
                    }
                });
                $db->setEventsManager($eventsManager);
            }

            return $db;
        });

        // Hbase
        $di->setShared('hbase', function () use ($configs) {
            if (isset($configs->hbase)) {
                $options = $configs->hbase->toArray();
                return new Hbase($options);
            } else {
                throw new \Exception('Hbase Config Valid');
            }
        });

        $this->application->setDI($di);
    }

    /**
     * @param array $arguments
     * @throws
     */
    public function run($arguments = array())
    {
        $this->autoLoader();
        $this->services();

        if ($this->mode === 'CLI') {
            if (empty($arguments)) {
                throw \Exception('CLI Require Arguments');
            }
            $this->application->handle($arguments);
        } else {
            echo $this->application->handle()->getContent();
        }
    }
}
