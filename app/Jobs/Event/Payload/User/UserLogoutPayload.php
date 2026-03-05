<?php

namespace App\Jobs\Event\Payload\User;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 退出登录
 */
class UserLogoutPayload extends BasePayload
{
    public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public static function getDescription(): string
    {
        return '用户-退出登录';
    }
}
