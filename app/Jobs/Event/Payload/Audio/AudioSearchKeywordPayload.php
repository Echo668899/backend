<?php

namespace App\Jobs\Event\Payload\Audio;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 有声-关键字搜索
 */
class AudioSearchKeywordPayload extends BasePayload
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
        return '有声-关键字搜索';
    }
}
