<?php

namespace App\Jobs\Event\Payload\User;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 用户-金币-充值成功
 */
class UserDoRechargeSuccessPayload extends BasePayload
{
    public $userId;
    public $orderId;
    public $groupId;
    public $paymentId;

    public function __construct($userId, $orderId, $groupId, $paymentId)
    {
        $this->userId    = $userId;
        $this->orderId   = $orderId;
        $this->groupId   = $groupId;
        $this->paymentId = $paymentId;
    }

    public static function getDescription(): string
    {
        return '用户-金币充值成功';
    }
}
