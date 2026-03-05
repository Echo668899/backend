<?php

namespace App\Jobs\Event\Payload\Common;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 评论
 */
class CommentPayload extends BasePayload
{
    public $userId;
    public $objectType;
    public $objectId;
    public $content;

    public function __construct($userId, $objectType, $objectId, $content)
    {
        $this->userId     = $userId;
        $this->objectType = $objectType;
        $this->objectId   = $objectId;
        $this->content    = $content;
    }

    public static function getDescription(): string
    {
        return '发布评论一次';
    }
}
