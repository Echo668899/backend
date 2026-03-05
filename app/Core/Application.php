<?php

namespace App\Core;

use App\Core\Mongodb\MongoDbConnection;
use App\Core\Services\LocaleService;
use App\Core\Services\RabbitMQService;
use Phalcon\Config\Adapter\Ini;
use Phalcon\Storage\SerializerFactory;

abstract class Application
{
    public const CACHE_DIR    = RUNTIME_PATH . '/cache/';
    public const LOGS_DIR     = RUNTIME_PATH . '/logs/';
    public const VIEWS_DIR    = RUNTIME_PATH . '/compiled/';
    public const SESSIONS_DIR = RUNTIME_PATH . '/sessions/';
    /** @var \Phalcon\Di\DiInterface */
    protected $container;

    public function __construct()
    {
        $this->initRuntime();
        $this->initDi();
        $this->initEnv();
        $this->initRedis();
        $this->initCache();
        $this->initMongodb();
        $this->initRabbitMQ();
        $this->initLocale();
    }

    /**
     * 初始化临时文件夹
     */
    protected function initRuntime()
    {
        if (!file_exists(self::CACHE_DIR)) {
            mkdir(self::CACHE_DIR, 0777, true);
        }
        if (!file_exists(self::LOGS_DIR)) {
            mkdir(self::LOGS_DIR, 0777, true);
        }
        if (!file_exists(self::VIEWS_DIR)) {
            mkdir(self::VIEWS_DIR, 0777, true);
        }
        if (!file_exists(self::SESSIONS_DIR)) {
            mkdir(self::SESSIONS_DIR, 0777, true);
        }
    }

    /**
     * 初始化容器
     * @return void
     */
    protected function initDi()
    {
        if (is_cli()) {
            $this->container = new \Phalcon\Di\FactoryDefault\Cli();
        } else {
            $this->container = new \Phalcon\Di\FactoryDefault();
        }
    }

    /**
     * 初始化配置
     */
    protected function initEnv()
    {
        $configFile = BASE_PATH . '.env';
        if (!file_exists($configFile)) {
            exit('Please copy .env.example to .env!');
        }
        $env = new Ini($configFile);
        $this->container->setShared('env', $env);
        date_default_timezone_set($env->path('app.timezone'));
        error_reporting(E_ALL | ~E_NOTICE | ~E_WARNING);
        register_shutdown_function('appErrorHandler') or set_error_handler('appErrorHandler', E_ALL);
        define('kDevMode', $env->path('app.env') === strtolower('dev'));
        define('kProdMode', $env->path('app.env') === strtolower('prod'));
    }

    /**
     * 初始化Redis
     */
    protected function initRedis()
    {
        $this->container->setShared('redis', function () {
            $config = env()->path('redis')->toArray();
            $host   = $config['host'] ?: '127.0.0.1';
            $port   = $config['port'] ?: 6379;
            $prefix = $config['prefix'] ?: 'ph_';
            $index  = $config['index'] ?? 0;
            $redis  = new \Redis();
            $redis->connect($host, $port);
            $redis->select($index);
            $redis->setOption(\Redis::OPT_PREFIX, $prefix);
            return $redis;
        });
    }

    /**
     * 初始化Cache
     */
    protected function initCache()
    {
        $this->container->setShared('cache', function () {
            $config            = env()->path('cache')->toArray();
            $serializerFactory = new SerializerFactory();
            switch ($config['adapter']) {
                case 'apcu':
                    return new \Phalcon\Cache\Adapter\Apcu($serializerFactory, $config);
                    break;
                case 'memcached':
                    return new \Phalcon\Cache\Adapter\Libmemcached($serializerFactory, $config);
                    break;
                case 'redis':
                    return new \Phalcon\Cache\Adapter\Redis($serializerFactory, $config);
                    break;
                default:
                    $config['storageDir'] = self::CACHE_DIR;
                    return new \Phalcon\Cache\Adapter\Stream($serializerFactory, $config);
                    break;
            }
        });
    }

    protected function initMongodb()
    {
        foreach (env()->toArray() as $key => $config) {
            if (strpos($key, 'database.mongodb') === false) {
                continue;
            }
            $name = str_replace('database.mongodb.', '', $key);
            $this->container->setShared("mongodb_{$name}", function () use ($config) {
                return new MongoDbConnection($config);
            });
        }
        return $this;
    }

    protected function initRabbitMQ()
    {
        $this->container->setShared('rabbitmq', function () {
            $config = env()->path('rabbitmq')->toArray();
            return new RabbitMQService($config);
        });
        return $this;
    }

    protected function initLocale(): void
    {
        $this->container->setShared('locale', function () {
            return new LocaleService();
        });
    }
}
