<?php

namespace App\Jobs\Event\Payload\User;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 登录
 */
class UserLoginPayload extends BasePayload
{
    public $userId;
    public $accountType;
    public $deviceType;

    public function __construct($userId, $accountType, $deviceType)
    {
        $this->userId      = $userId;
        $this->accountType = $accountType;
        $this->deviceType  = $deviceType;
    }

    public static function getDescription(): string
    {
        return '用户-登录一次';
    }
}
