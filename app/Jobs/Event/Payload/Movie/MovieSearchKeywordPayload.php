<?php

namespace App\Jobs\Event\Payload\Movie;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 视频-关键字搜索
 */
class MovieSearchKeywordPayload extends BasePayload
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
        return '视频-关键字搜索';
    }
}
