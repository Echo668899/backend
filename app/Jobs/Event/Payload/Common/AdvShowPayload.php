<?php

namespace App\Jobs\Event\Payload\Common;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 展示广告
 */
class AdvShowPayload extends BasePayload
{
    public $userId;
    public $objectType;
    public $objectId;

    public function __construct($userId, $objectType, $objectId)
    {
        $this->userId     = $userId;
        $this->objectType = $objectType;
        $this->objectId   = $objectId;
    }

    public static function getDescription(): string
    {
        return '广告展示一次';
    }
}
