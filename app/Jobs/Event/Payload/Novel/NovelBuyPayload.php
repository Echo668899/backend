<?php

namespace App\Jobs\Event\Payload\Novel;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 小说-购买
 */
class NovelBuyPayload extends BasePayload
{
    public $userId;
    public $novelId;
    public $orderSn;
    public $num;
    public $oldMoney;
    public $newMoney;

    public function __construct($userId, $novelId, $orderSn, $num, $oldMoney, $newMoney)
    {
        $this->userId   = $userId;
        $this->novelId  = $novelId;
        $this->orderSn  = $orderSn;
        $this->num      = $num;
        $this->oldMoney = $oldMoney;
        $this->newMoney = $newMoney;
    }

    public static function getDescription(): string
    {
        return '小说-购买';
    }
}
