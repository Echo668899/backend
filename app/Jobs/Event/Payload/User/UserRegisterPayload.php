<?php

namespace App\Jobs\Event\Payload\User;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 注册
 */
class UserRegisterPayload extends BasePayload
{
    public $userId;
    public $accountType;
    public $deviceType;
    public $deviceVersion;
    public $channelCode;
    public $shareCode;
    public $registerAt;

    public function __construct($userId, $accountType, $deviceType, $deviceVersion, $channelCode, $shareCode, $registerAt)
    {
        $this->userId        = $userId;
        $this->accountType   = $accountType;
        $this->deviceType    = $deviceType;
        $this->deviceVersion = $deviceVersion;
        $this->channelCode   = $channelCode;
        $this->shareCode     = $shareCode;
        $this->registerAt    = $registerAt;
    }

    public static function getDescription(): string
    {
        return '用户-注册账户';
    }
}
