<?php

namespace App\Jobs\Event\Payload\User;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 用户-会员
 */
class UserVipPayload extends BasePayload
{
    public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public static function getDescription(): string
    {
        return '用户-进入会员充值';
    }
}
