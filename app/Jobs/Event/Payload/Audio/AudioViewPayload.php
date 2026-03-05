<?php

namespace App\Jobs\Event\Payload\Audio;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 有声-观看一次
 */
class AudioViewPayload extends BasePayload
{
    public $userId;
    public $audioId;
    public string $chapterId;

    public function __construct($userId, $audioId, $chapterId)
    {
        $this->userId    = $userId;
        $this->audioId   = $audioId;
        $this->chapterId = $chapterId;
    }

    public static function getDescription(): string
    {
        return '有声-观看一次';
    }
}
