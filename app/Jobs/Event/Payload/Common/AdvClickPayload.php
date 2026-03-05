<?php

namespace App\Jobs\Event\Payload\Common;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 点击广告
 */
class AdvClickPayload extends BasePayload
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
        return '广告点击一次';
    }
}
