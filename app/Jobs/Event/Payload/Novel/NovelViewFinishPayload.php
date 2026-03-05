<?php

namespace App\Jobs\Event\Payload\Novel;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 小说-阅读完成
 */
class NovelViewFinishPayload extends BasePayload
{
    public $userId;
    public string $novelId;

    public function __construct($userId, $novelId)
    {
        $this->userId  = $userId;
        $this->novelId = $novelId;
    }

    public static function getDescription(): string
    {
        return '小说-阅读完成';
    }
}
