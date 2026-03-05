<?php

namespace App;

use App\Core\Application;
use App\Exception\BusinessException;
use App\Exception\Handler\AppExceptionHandler;
use Phalcon\Crypt;
use Phalcon\Dispatcher\Exception as DispatcherException;
use Phalcon\Http\Response\Cookies;
use Phalcon\Session\Manager;
use Phalcon\Storage\AdapterFactory;
use Phalcon\Storage\SerializerFactory;

class WebApplication extends Application
{
    public function run()
    {
        $this->initCookie();
        $this->initSession();
        $this->initView();
        $this->initRouter();
        $this->initDispatcher();
        $application = new \Phalcon\Mvc\Application($this->container);
        $this->initModule($application);

        try {
            $response = $application->handle($_SERVER['REQUEST_URI']);
            $response->send();
        } catch (\Error $e) {
            new AppExceptionHandler(new \Exception($e->getMessage() . $e->getFile() . $e->getLine(), -2));
        } catch (BusinessException|\Exception $e) {
            new AppExceptionHandler($e);
        }
    }

    /**
     * 初始化路由
     */
    public function initRouter()
    {
        $this->container->setShared('router', function () {
            $modules = env()->path('modules')->toArray();
            $router  = new \Phalcon\Mvc\Router(false);

            // /非常重要!!! 优先匹配后生成的路由
            uasort($modules, function ($a, $b) {
                return strlen($a) - strlen($b);
            });
            foreach ($modules as $module => $basePath) {
                $group = new \Phalcon\Mvc\Router\Group([
                    'module'    => $module,
                    'namespace' => '\App\Controller\\' . $module,
                ]);

                if ($basePath != '/') {
                    // 设置前缀
                    $group->setPrefix($basePath);
                }

                $group->add('/', ['controller' => 'index', 'action' => 'index']);

                $group->add('/:controller', ['controller' => 1, 'action' => 'index']);

                $group->add('/:controller/:action', ['controller' => 1, 'action' => 2]);

                $group->add('/:controller/:action/:params', ['controller' => 1, 'action' => 2, 'params' => 3]);

                // 注册路由组
                $router->mount($group);
            }

            return $router;
        });

        // /扩展url
        $this->container->setShared('url', function () {
            return new \App\Core\Url();
        });
    }

    /**
     * 初始化分发器
     */
    public function initDispatcher()
    {
        $eventsManager = $this->container->getShared('eventsManager');

        // 加载前处理的事件
        $eventsManager->attach('dispatch:beforeDispatchLoop', function ($event, \Phalcon\Mvc\Dispatcher $dispatcher) {
            $moduleName = $dispatcher->getModuleName();
            if (env()->path("modules.{$moduleName}")) {
                url()->setBaseUri(env()->path("modules.{$moduleName}"));
                url()->setStaticBaseUri(env()->path("modules.{$moduleName}"));
                container()->get('view')->setViewsDir(APP_PATH . '/Views/' . $moduleName . '/default');
            }
        });

        // 加载异常处理的事件
        $eventsManager->attach(
            'dispatch:beforeException',
            function ($event, $dispatcher, $exception) {
                switch ($exception->getCode()) {
                    case DispatcherException::EXCEPTION_ACTION_NOT_FOUND:
                        header('HTTP/1.1 404 Not Found');
                        header('Status: 404 Not Found');
                        exit('404-2');
                        break;
                    case DispatcherException::EXCEPTION_CYCLIC_ROUTING:
                        exit('404-3');
                        break;
                    case DispatcherException::EXCEPTION_HANDLER_NOT_FOUND:
                        header('HTTP/1.1 404 Not Found');
                        header('Status: 404 Not Found');
                        exit('404-1');
                        break;
                }
            }
        );

        // 加载完成处理的事件
        $eventsManager->attach('dispatch:afterDispatchLoop', function ($event, $dispatcher) {
        });
        $dispatcher = new \Phalcon\Mvc\Dispatcher();
        $dispatcher->setDI($this->container);
        $dispatcher->setEventsManager($eventsManager);
        $this->container->setShared('dispatcher', $dispatcher);
    }

    /**
     * 初始化模块
     * @param  \Phalcon\Mvc\Application $application
     * @return void
     */
    public function initModule(\Phalcon\Mvc\Application $application)
    {
        $modules = env()->path('modules')->toArray();
        foreach ($modules as $module => $basePath) {
            $application->registerModules([
                $module => [
                    'className' => \App\Core\Module::class,
                    'path'      => APP_PATH . '/Core/Module.php',
                ]
            ], true);
        }
    }

    /**
     * 初始化cookie
     */
    protected function initCookie()
    {
        $this->container->setShared('cookies', function () {
            return new Cookies(false);
        });
    }

    /**
     * 初始化session
     */
    protected function initSession()
    {
        $this->container->setShared('session', function () {
            $adapter           = env()->path('session.adapter');
            $config            = env()->path('session.' . $adapter)->toArray();
            $config['prefix']  = $config['prefix'] ?: 'ph_';
            $session           = new Manager();
            $serializerFactory = new SerializerFactory();
            $adapterFactory    = new AdapterFactory($serializerFactory);
            switch ($adapter) {
                case 'memcached':
                    $libMemcached = new \Phalcon\Session\Adapter\Libmemcached($adapterFactory, $config);
                    $session->setAdapter($libMemcached)->start();
                    break;
                case 'redis':
                    $redis = new \Phalcon\Session\Adapter\Redis($adapterFactory, $config);
                    $session->setAdapter($redis)->start();
                    break;
                default:
                    $files = new \Phalcon\Session\Adapter\Stream([
                        'prefix'   => $config['prefix'],
                        'savePath' => self::SESSIONS_DIR,
                    ]);
                    $session->setAdapter($files)->start();
                    break;
            }
            return $session;
        });
    }

    /**
     * 初始化视图
     */
    protected function initView()
    {
        $view = new \Phalcon\Mvc\View();
        $view->setDI($this->container);
        $phtml = new \Phalcon\Mvc\View\Engine\Php($view, $this->container);
        $volt  = new \Phalcon\Mvc\View\Engine\Volt($view, $this->container);

        $volt->setOptions(['always' => true, 'extension' => '.volt', 'path' => self::VIEWS_DIR]);
        $volt->getCompiler()->addFunction('uniqid', 'uniqid');
        $volt->getCompiler()->addFunction('createStaticUrl', 'createStaticUrl');

        $view->registerEngines([
            '.phtml' => $phtml,
            '.volt'  => $volt,
        ]);
        $this->container->setShared('view', $view);
    }

    /**
     * 初始化cookie
     */
    protected function initCrypt()
    {
        $this->container->setShared('crypt', function () {
            return new Crypt('======This is key======');
        });
    }
}
