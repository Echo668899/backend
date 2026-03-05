<?php

namespace App\Jobs\Event\Payload\Comics;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 漫画-关键字搜索
 */
class ComicsSearchKeywordPayload extends BasePayload
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
        return '漫画-关键字搜索';
    }
}
