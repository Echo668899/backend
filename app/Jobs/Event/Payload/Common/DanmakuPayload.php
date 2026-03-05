<?php

namespace App\Jobs\Event\Payload\Common;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 弹幕
 */
class DanmakuPayload extends BasePayload
{
    public $userId;
    public $objectType;
    public $objectId;
    public $pos;

    public function __construct($userId, $objectType, $objectId, $pos)
    {
        $this->userId     = $userId;
        $this->objectType = $objectType;
        $this->objectId   = $objectId;
        $this->pos        = $pos;
    }

    public static function getDescription(): string
    {
        return '发布弹幕一次';
    }
}
