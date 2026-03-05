<?php

namespace App\Jobs\Event\Payload\Comics;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 漫画-购买
 */
class ComicsBuyPayload extends BasePayload
{
    public $userId;
    public $comicsId;
    public $orderSn;
    public $num;
    public $oldMoney;
    public $newMoney;

    public function __construct($userId, $comicsId, $orderSn, $num, $oldMoney, $newMoney)
    {
        $this->userId   = $userId;
        $this->comicsId = $comicsId;
        $this->orderSn  = $orderSn;
        $this->num      = $num;
        $this->oldMoney = $oldMoney;
        $this->newMoney = $newMoney;
    }

    public static function getDescription(): string
    {
        return '漫画-购买';
    }
}
