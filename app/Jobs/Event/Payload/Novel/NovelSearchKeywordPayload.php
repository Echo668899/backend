<?php

namespace App\Jobs\Event\Payload\Novel;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 小说-关键字搜索
 */
class NovelSearchKeywordPayload extends BasePayload
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
        return '小说-关键字搜索';
    }
}
