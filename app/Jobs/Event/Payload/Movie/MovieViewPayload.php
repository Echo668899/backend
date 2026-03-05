<?php

namespace App\Jobs\Event\Payload\Movie;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 视频-观看一次
 */
class MovieViewPayload extends BasePayload
{
    public $userId;
    public $movieId;
    public $linkId;
    public $showTime;
    public $playTime;
    public $isDayFirst;

    public function __construct($userId, $movieId, $linkId, $showTime, $playTime, $isDayFirst)
    {
        $this->userId     = $userId;
        $this->movieId    = $movieId;
        $this->linkId     = $linkId;
        $this->showTime   = $showTime;
        $this->playTime   = $playTime;
        $this->isDayFirst = $isDayFirst;
    }

    public static function getDescription(): string
    {
        return '视频-观看一次';
    }
}
