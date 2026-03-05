<?php

namespace App\Jobs\Event\Payload\Post;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 帖子-收藏
 */
class PostFavoritePayload extends BasePayload
{
    public $userId;
    public $postId;
    public bool $status;

    public function __construct($userId, $postId, $status)
    {
        $this->userId = $userId;
        $this->postId = $postId;
        $this->status = $status;
    }

    public static function getDescription(): string
    {
        return '帖子-收藏|取消收藏';
    }
}
