<?php

namespace App\Jobs\Event\Payload\Movie;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 视频-点赞
 */
class MovieLovePayload extends BasePayload
{
    public $userId;
    public $movieId;
    public bool $status;

    public function __construct($userId, $movieId, $status)
    {
        $this->userId  = $userId;
        $this->movieId = $movieId;
        $this->status  = $status;
    }

    public static function getDescription(): string
    {
        return '视频-点赞|取消点赞';
    }
}
