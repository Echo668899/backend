<?php

namespace App\Jobs\Event\Payload\Movie;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 视频-观看完成
 */
class MovieViewCompletePayload extends BasePayload
{
    public $userId;
    public $movieId;
    public $linkId;
    public $playTime;
    public $viewTime;

    public function __construct($userId, $movieId, $linkId, $playTime, $viewTime)
    {
        $this->userId   = $userId;
        $this->movieId  = $movieId;
        $this->linkId   = $linkId;
        $this->playTime = $playTime;
        $this->viewTime = $viewTime;
    }

    public static function getDescription(): string
    {
        return '视频-观看完成';
    }
}
