<?php

namespace App\Jobs\Event\Payload\Movie;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 视频-观看完成
 */
class MovieViewFinishPayload extends BasePayload
{
    public $userId;
    public $movieId;
    public $linkId;

    public function __construct($userId, $movieId, $linkId)
    {
        $this->userId  = $userId;
        $this->movieId = $movieId;
        $this->linkId  = $linkId;
    }

    public static function getDescription(): string
    {
        return '视频-观看完成';
    }
}
