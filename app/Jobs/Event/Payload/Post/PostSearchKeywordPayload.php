<?php

namespace App\Jobs\Event\Payload\Post;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 社区-关键字搜索
 */
class PostSearchKeywordPayload extends BasePayload
{
    public $userId;
    public $keywords;
    public $resultCount;

    public function __construct($userId, $keywords, $resultCount)
    {
        $this->userId      = $userId;
        $this->keywords    = $keywords;
        $this->resultCount = $resultCount;
    }

    public static function getDescription(): string
    {
        return '社区-关键字搜索';
    }
}
