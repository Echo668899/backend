<?php

namespace App\Jobs\Event\Payload\Movie;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 视频-购买
 */
class MovieBuyPayload extends BasePayload
{
    public $userId;
    public $movieId;
    public $orderSn;
    public $num;
    public $oldMoney;
    public $newMoney;

    public function __construct($userId, $movieId, $orderSn, $num, $oldMoney, $newMoney)
    {
        $this->userId   = $userId;
        $this->movieId  = $movieId;
        $this->orderSn  = $orderSn;
        $this->num      = $num;
        $this->oldMoney = $oldMoney;
        $this->newMoney = $newMoney;
    }

    public static function getDescription(): string
    {
        return '视频-购买';
    }
}
