<?php

namespace App\Jobs\Event\Payload\Ai;

use App\Jobs\Event\Payload\BasePayload;

/**
 * AI-图转视频
 */
class AiImageToVideoPayload extends BasePayload
{
    public $userId;
    public $orderId;
    public $type;
    public $num;
    public $oldMoney;
    public $newMoney;

    public function __construct($userId, $orderId, $type, $num, $oldMoney, $newMoney)
    {
        $this->userId   = $userId;
        $this->orderId  = $orderId;
        $this->type     = $type;
        $this->num      = $num;
        $this->oldMoney = $oldMoney;
        $this->newMoney = $newMoney;
    }

    public static function getDescription(): string
    {
        return 'AI-图转视频订单';
    }
}
