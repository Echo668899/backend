<?php

namespace App\Jobs\Event\Payload\Post;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 贴子-点赞
 */
class PostLovePayload extends BasePayload
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
        return '帖子-点赞|取消点赞';
    }
}
