<?php

namespace App\Jobs\Event\Payload\Comics;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 漫画-点赞
 */
class ComicsLovePayload extends BasePayload
{
    public $userId;
    public $comicsId;
    public bool $status;

    public function __construct($userId, $comicsId, $status)
    {
        $this->userId   = $userId;
        $this->comicsId = $comicsId;
        $this->status   = $status;
    }

    public static function getDescription(): string
    {
        return '漫画-点赞|取消点赞';
    }
}
