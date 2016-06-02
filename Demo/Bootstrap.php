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
use Phalcon\Cache\Frontend\Data;
use Phalcon\Cache\Backend\Memcache;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File;
use Phalcon\Logger\Formatter\Line;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Model\MetaData\Memcache as MetaDataMemcache;
use Phalcon\Mvc\Model\MetaData\Redis as MetaDataRedis;
use Phalcon\Session\Adapter\Memcache as SessionMemcache;
use Phalcon\Session\Adapter\Files as SessionFiles;

/**
 * Class Bootstrap
 *
 */
class Bootstrap
{
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
     * DI
     *
     * @var
     */
    protected $di;

    /**
     * 构造函数
     *
     * @param $mode
     * @throws \Exception
     */
    public function __construct($mode = '')
    {
        $this->initApplication();
        $this->mode = $mode;
        $this->application = $this->mode === 'CLI' ? new Console() : new Application();
    }

    /**
     * 初始化工作
     *
     */
    public function initApplication()
    {
        do {
            // 配置检查
            $appConfigs = array('logger', 'bootstrap');
            foreach ($appConfigs as $config) {
                if (loadPath($config) === false) {
                    exit('missing basic config ' . $config);
                }
            }
        } while (0);
    }

    /**
     * 注册命名空间
     *
     */
    protected function autoLoader()
    {
        $loader = new Loader();
        $bootstrap = load('bootstrap');
        $namespaces = $bootstrap['namespaces'];
        $loader->registerNamespaces($namespaces)->register();
    }

    /**
     * 默认服务依赖注入
     *
     */
    protected function commonServices()
    {
        $mode = $this->mode;

        $di = $this->mode === 'CLI' ? new Cli() : new FactoryDefault();

        // 日志
        $di->set('logger', function () {
            $config = load('logger');
            $adapter = $config['adapter'];
            $filename = $config[$adapter]['filename'];
            $filedir = dirname($filename);

            if (empty($config)) {
                throw new \Exception('logger config Require failed');
            }
            if (!is_dir($filedir)) {
                mkdir($filedir, 0755, true);
            }
            $logger = new File($filename);
            $formatter = new Line(null, 'Y-m-d H:i:s');
            $loglevel = config('app.loglevel');
            $logger->setFormatter($formatter);
            $logger->setLogLevel($loglevel ? $loglevel : \Phalcon\Logger::ERROR);

            return $logger;
        }, true);
        $this->application->setDI($di);


        // 命名空间
        $di->set('dispatcher', function () use ($mode) {
            $dispatcher = new Dispatcher();
            $dispatcher = $mode === 'CLI' ? new \Phalcon\CLI\Dispatcher() : new Dispatcher();
            $bootstrap = load('bootstrap');
            $default = $bootstrap['dispatcher'];
            $dispatcher->setDefaultNamespace($mode === 'CLI' ? $default['cli'] : $default['default']);

            return $dispatcher;
        }, true);

        // 路由
        if ($load = load('router', null, true)) {
            if ($load instanceof Router) {
                $di->set('router', $load);
            }
        }

        // 视图
        $di->set('view', function () {
            $view = new View();
            $view->setViewsDir(APP_VIEW);

            return $view;
        }, true);

        // 加解密
        if ($config = config('crypt')) {
            $di->set('crypt', function () use ($config) {
                $crypt = new Crypt();
                $crypt->setKey($config['authkey']);

                return $crypt;
            }, true);
        }

        // 默认缓存
        if ($config = config('cache')) {
            $di->set('cache', function () use ($config) {
                $cache = null;
                $adapter = strtolower($config['adapter']);
                $options = $config[$adapter];
                $frontend = new Data(array('lifetime' => $config['lifetime']));
                switch ($adapter) {
                    case 'memcache' :
                        $cache = new Memcache($frontend, $options);
                        break;
                    case 'redis':
                        if (empty($options['auth'])) {
                            unset($options['auth']);
                        }
                        $cache = new \Phalcon\Extend\Cache\Backend\Redis($frontend, $options);
                        break;
                }

                return $cache;
            }, true);
        }

        // Cookies
        if ($config = config('cookies')) {
            $di->set('cookies', function () use ($config) {
                $cookies = new \Phalcon\Extend\Http\Response\Cookies($config);
                if (!config('crypt.authkey')) {
                    $cookies->useEncryption(false);
                }

                return $cookies;
            }, true);
        }

        // Session
        if ($config = config('session')) {
            $di->set('session', function () use ($config) {
                if (!empty($config['options'])) {
                    foreach ($config['options'] as $name => $value) {
                        ini_set("session.$name", $value);
                    }
                }
                $adapter = strtolower($config['adapter']);
                $options = $config[$adapter];
                switch ($adapter) {
                    case 'memcache' :
                        $session = new SessionMemcache($options);
                        break;
                    case 'redis' :
                        $session = new \Phalcon\Extend\Session\Adapter\Redis($options);
                        break;
                    default :
                        $session = new SessionFiles();
                        break;
                }
                $session->start();

                return $session;
            }, true);
        }

        // Db
        if ($config = config('db')) {
            $di->set('db', function () use ($config) {
                $mysql = new Mysql($config);
                if (debugMode()) {
                    $eventsManager = new Manager();
                    $logger = new File(APP_LOG . DS . 'Mysql' . LOGEXT);
                    $formatter = new Line(null, 'Y-m-d H:i:s');
                    $logger->setFormatter($formatter);
                    $eventsManager->attach('db', function ($event, $mysql) use ($logger) {
                        if ($event->getType() == 'beforeQuery') {
                            $logger->log($mysql->getSQLStatement(), Logger::INFO);
                        }
                        if ($event->getType() == 'afterQuery') {
                        }
                    });

                    $mysql->setEventsManager($eventsManager);
                }

                return $mysql;
            }, true);
        }

        // DB 元信息
        if ($config = config('metadata')) {
            $di->set('modelsMetadata', function () use ($config) {
                $modelsMetadata = null;
                $adapter = strtolower($config['adapter']);
                $options = $config[$adapter];
                switch ($adapter) {
                    case 'memcache' :
                        $modelsMetadata = new MetaDataMemcache($options);
                        break;
                    case 'redis':
                        if (empty($options['auth'])) {
                            unset($options['auth']);
                        }
                        $modelsMetadata = new MetaDataRedis($options);
                        break;
                }

                return $modelsMetadata;
            }, true);
        }

        $this->application->setDI($di);
    }

    /**
     * 自定义服务注入
     *
     */
    protected function customServices()
    {
        $di = $this->application->getDI();

        // 自定义服务

        $this->application->setDI($di);
    }

    /**
     * @param array $arguments
     * @throws
     */
    public function run($arguments = array())
    {
        $this->autoLoader();
        $this->commonServices();
        $this->customServices();

        if ($this->mode === 'CLI') {
            if (empty($arguments)) {
                throw new \Exception('CLI Require Arguments');
            }
            $this->application->handle($arguments);
        } else {
            echo $this->application->handle()->getContent();
        }
    }
}
