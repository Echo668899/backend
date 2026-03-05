<?php

namespace App\Jobs\Event\Payload;

abstract class BasePayload
{
    /**
     * 事件名称
     * @return string
     */
    public function getEventName(): string
    {
        return __CLASS__;
    }

    abstract public static function getDescription(): string;
}
