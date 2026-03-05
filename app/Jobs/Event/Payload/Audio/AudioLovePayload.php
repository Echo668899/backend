<?php

namespace App\Jobs\Event\Payload\Audio;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 有声-点赞
 */
class AudioLovePayload extends BasePayload
{
    public $userId;
    public $audioId;
    public bool $status;

    public function __construct($userId, $audioId, $status)
    {
        $this->userId  = $userId;
        $this->audioId = $audioId;
        $this->status  = $status;
    }

    public static function getDescription(): string
    {
        return '有声-点赞|取消点赞';
    }
}
