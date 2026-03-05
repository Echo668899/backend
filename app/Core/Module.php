<?php

namespace App\Core;

use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{
    public function registerAutoloaders(\Phalcon\Di\DiInterface $container = null)
    {
    }

    public function registerServices(\Phalcon\Di\DiInterface $container)
    {
    }
}
