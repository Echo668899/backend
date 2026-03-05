<?php

namespace App\Jobs\Event\Payload\Audio;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 有声-购买
 */
class AudioBuyPayload extends BasePayload
{
    public $userId;
    public $audioId;
    public $orderSn;
    public $num;
    public $oldMoney;
    public $newMoney;

    public function __construct($userId, $audioId, $orderSn, $num, $oldMoney, $newMoney)
    {
        $this->userId   = $userId;
        $this->audioId  = $audioId;
        $this->orderSn  = $orderSn;
        $this->num      = $num;
        $this->oldMoney = $oldMoney;
        $this->newMoney = $newMoney;
    }

    public static function getDescription(): string
    {
        return '有声-购买';
    }
}
