<?php

namespace App;

use App\Core\Application;
use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;

class CliApplication extends Application
{
    public function run()
    {
        set_time_limit(0);
        @ini_set('memory_limit', '512M');
        try {
            $this->initDispatcher();
            $this->initUrl();
            $console   = new Console($this->container);
            $arguments = [];
            global $argv;
            foreach ($argv as $k => $arg) {
                if ($k === 1) {
                    $arguments['task'] = $arg;
                } elseif ($k === 2) {
                    $arguments['action'] = $arg;
                } elseif ($k >= 3) {
                    $arguments['params'][] = $arg;
                }
            }
            $console->handle($arguments);
        } catch (\Exception $e) {
            dd(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }
    }

    public function initDispatcher()
    {
        $dispatcher = new Dispatcher();
        $dispatcher->setDefaultNamespace('App\Tasks');
        $this->container->setShared('dispatcher', $dispatcher);
    }

    public function initUrl()
    {
        $this->container->setShared('url', function () {
            $url = new \App\Core\Url();
            $url->setBaseUri('');
            return $url;
        });
    }
}
