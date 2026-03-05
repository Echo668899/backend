<?php

namespace App\Jobs\Event\Payload\Novel;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 小说-收藏
 */
class NovelFavoritePayload extends BasePayload
{
    public $userId;
    public $novelId;
    public bool $status;

    public function __construct($userId, $novelId, $status)
    {
        $this->userId  = $userId;
        $this->novelId = $novelId;
        $this->status  = $status;
    }
    public static function getDescription(): string
    {
        return '小说-收藏|取消收藏';
    }
}
