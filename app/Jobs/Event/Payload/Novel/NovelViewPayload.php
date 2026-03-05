<?php

namespace App\Jobs\Event\Payload\Novel;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 小说-观看一次
 */
class NovelViewPayload extends BasePayload
{
    public $userId;
    public $novelId;
    public $chapterId;

    public function __construct($userId, $novelId, $chapterId)
    {
        $this->userId    = $userId;
        $this->novelId   = $novelId;
        $this->chapterId = $chapterId;
    }

    public static function getDescription(): string
    {
        return '小说-阅读一次';
    }
}
