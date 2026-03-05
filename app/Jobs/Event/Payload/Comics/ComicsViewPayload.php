<?php

namespace App\Jobs\Event\Payload\Comics;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 漫画-观看一次
 */
class ComicsViewPayload extends BasePayload
{
    public $userId;
    public $comicsId;

    public function __construct($userId, $comicsId)
    {
        $this->userId   = $userId;
        $this->comicsId = $comicsId;
    }

    public static function getDescription(): string
    {
        return '漫画-观看一次';
    }
}
