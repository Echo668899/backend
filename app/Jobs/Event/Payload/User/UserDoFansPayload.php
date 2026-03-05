<?php

namespace App\Jobs\Event\Payload\User;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 用户粉丝
 */
class UserDoFansPayload extends BasePayload
{
    public $userId;
    public $homeId;
    public $action;

    public function __construct($userId, $homeId, $action)
    {
        $this->userId = $userId;
        $this->homeId = $homeId;
        $this->action = $action;
    }

    public static function getDescription(): string
    {
        return '用户-粉丝|取消';
    }
}
