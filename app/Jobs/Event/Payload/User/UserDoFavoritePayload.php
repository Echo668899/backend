<?php

namespace App\Jobs\Event\Payload\User;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 用户收藏
 * 收藏板块
 */
class UserDoFavoritePayload extends BasePayload
{
    public $userId;
    public $objectType;
    public $objectId;
    public bool $status;

    public function __construct($userId, $objectType, $objectId, bool $status)
    {
        $this->userId     = $userId;
        $this->objectType = $objectType;
        $this->objectId   = $objectId;
        $this->status     = $status;
    }

    public static function getDescription(): string
    {
        return '用户-收藏板块|取消收藏板块';
    }
}
