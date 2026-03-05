<?php

use Phalcon\Di\FactoryDefault;

/**
 * 容器实例
 */
if (!function_exists('container')) {
    /**
     * 获取当前容器
     * @return \Phalcon\Di\DiInterface|null
     */
    function container()
    {
        return FactoryDefault::getDefault();
    }
}

if (!function_exists('dispatcher')) {
    /**
     * 获取分发器
     * @return Phalcon\Mvc\Dispatcher|null
     */
    function dispatcher()
    {
        return container()->get('dispatcher');
    }
}

if (!function_exists('env')) {
    /**
     * 获取配置env
     * @return \Phalcon\Config\Adapter\Ini|null
     */
    function env()
    {
        return container()->get('env');
    }
}

if (!function_exists('cache')) {
    /**
     * 获取缓存
     * @return \Phalcon\Cache\Adapter\Redis|\Phalcon\Cache\Adapter\Apcu|\Phalcon\Cache\Adapter\Stream|\Phalcon\Cache\Adapter\Libmemcached
     */
    function cache()
    {
        return container()->get('cache');
    }
}

if (!function_exists('redis')) {
    /**
     * 获取缓存
     * @return \Redis
     */
    function redis()
    {
        return container()->get('redis');
    }
}

if (!function_exists('session')) {
    /**
     * 获取配置
     * @return \Phalcon\Session\Manager
     */
    function session()
    {
        return container()->get('session');
    }
}

if (!function_exists('url')) {
    /**
     * url
     * @return \App\Core\Url
     */
    function url()
    {
        return container()->get('url');
    }
}

if (!function_exists('router')) {
    /**
     * 路由
     * @return \Phalcon\Mvc\Router|\Phalcon\Mvc\RouterInterface
     */
    function router()
    {
        return container()->get('router');
    }
}

if (!function_exists('request')) {
    /**
     * request
     * @return \Phalcon\Http\Request|\Phalcon\Http\RequestInterface
     */
    function request()
    {
        return container()->get('request');
    }
}

if (!function_exists('is_cli')) {
    /**
     * 检查允许环境
     * @return bool
     */
    function is_cli()
    {
        return php_sapi_name() == 'cli';
    }
}

if (!function_exists('get_auto_class')) {
    /**
     * 容器
     * @param        $class
     * @return mixed
     */
    function get_auto_class($class)
    {
        if (!container()->has($class)) {
            container()->setShared($class, function () use ($class) {
                return new $class();
            });
        }
        return container()->getShared($class);
    }
}

if (!function_exists('cookies')) {
    /**
     * @return \Phalcon\Http\Response\Cookies|\Phalcon\Http\Response\CookiesInterface
     */
    function cookies()
    {
        return container()->get('cookies');
    }
}

if (!function_exists('locale')) {
    /**
     * @return App\Core\Services\LocaleService
     */
    function locale()
    {
        return container()->get('locale');
    }
}

if (! function_exists('__')) {
    /**
     * Translate the given message.
     * @param               $message
     * @return mixed|string
     */
    function __($message)
    {
        if (empty($message)) {
            return '';
        }

        try {
            return locale()->getTranslator()->_($message);
        } catch (\Exception $e) {
        }
        return $message;
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('format_num')) {
    function format_num($num, $p = 0)
    {
        if ($p == 0) {
            return sprintf('%u', $num / 100);
        }
        return sprintf('%.' . $p . 'f', $num / 100);
    }
}

if (!function_exists('appErrorHandler')) {
    /**错误日志
     * @param int $number
     * @param string $message
     * @param int $file
     * @param int $line
     * @return array
     */
    function appErrorHandler($number = 0, $message = '', $file = 0, $line = 0)
    {
        if ($number && $file) {
            $info            = [];
            $info['type']    = $number;
            $info['message'] = $message;
            $info['file']    = $file;
            $info['line']    = $line;
        } else {
            $info = error_get_last();
        }
        if ($info) {
            if ($info['type'] == E_NOTICE || $info['type'] == E_WARNING || $info['type'] == E_USER_NOTICE) {
                return [];
            }
            $dir        = RUNTIME_PATH . '/logs';
            $logMessage = 'Date:' . date('Y-m-d H:i:s') . PHP_EOL;
            $logMessage .= 'Type:' . $info['type'] . PHP_EOL;
            $logMessage .= 'Message:' . $info['message'] . PHP_EOL;
            $logMessage .= 'File:' . $info['file'] . PHP_EOL;
            $logMessage .= 'Line:' . $info['line'] . PHP_EOL . PHP_EOL;
            file_put_contents($dir . '/' . date('Y-m-d') . '.log', $logMessage, FILE_APPEND);
        }
        return [];
    }
}

if (!function_exists('createUrl')) {
    /**
     * 创建url
     * @param         $url
     * @param  array  $data
     * @param  string $module
     * @return string
     */
    function createUrl($url, $data = [], $module = null)
    {
        $module    = empty($module) ? dispatcher()->getModuleName() : $module;
        $moduleUrl = env()->path("modules.{$module}");
        $url       = $moduleUrl . $url;
        if ($data) {
            $url .= '?' . http_build_query($data);
        }
        return $url;
    }
}

if (!function_exists('createStaticUrl')) {
    /**
     * 获取静态资源
     * @param         $url
     * @return string
     */
    function createStaticUrl($url)
    {
        $version = \App\Services\Common\ConfigService::getConfig('static_version');
        if (empty($url)) {
            return '';
        }
        if (strpos($url, '?') > 0) {
            $url .= '&_v=' . $version;
        } else {
            $url .= '?_v=' . $version;
        }
        return $url;
    }
}
