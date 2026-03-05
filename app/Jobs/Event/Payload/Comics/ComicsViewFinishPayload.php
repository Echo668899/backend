<?php

namespace App\Jobs\Event\Payload\Comics;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 漫画-阅读完成
 */
class ComicsViewFinishPayload extends BasePayload
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
        return '漫画-阅读完成';
    }
}
