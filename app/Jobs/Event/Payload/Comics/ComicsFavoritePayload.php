<?php

namespace App\Jobs\Event\Payload\Comics;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 漫画-收藏
 */
class ComicsFavoritePayload extends BasePayload
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
        return '漫画-收藏|取消收藏';
    }
}
