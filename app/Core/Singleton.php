<?php

namespace App\Core;

abstract class Singleton
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return static
     */
    public static function instance()
    {
        $class = static::class;

        if (!container()->has($class)) {
            container()->setShared($class, function () use ($class) {
                return new $class();
            });
        }
        return container()->getShared($class);
    }
}
