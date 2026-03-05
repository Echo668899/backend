<?php

namespace App\Jobs\Event\Payload\Post;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 帖子-观看一次
 */
class PostViewPayload extends BasePayload
{
    public $userId;
    public $postId;

    public function __construct($userId, $postId)
    {
        $this->userId = $userId;
        $this->postId = $postId;
    }

    public static function getDescription(): string
    {
        return '帖子-观看一次';
    }
}
