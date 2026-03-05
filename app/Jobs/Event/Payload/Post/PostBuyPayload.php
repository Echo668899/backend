<?php

namespace App\Jobs\Event\Payload\Post;

use App\Jobs\Event\Payload\BasePayload;

/**
 * 贴子-购买
 */
class PostBuyPayload extends BasePayload
{
    public $userId;
    public $postId;
    public $orderSn;
    public $num;
    public $oldMoney;
    public $newMoney;

    public function __construct($userId, $postId, $orderSn, $num, $oldMoney, $newMoney)
    {
        $this->userId   = $userId;
        $this->postId   = $postId;
        $this->orderSn  = $orderSn;
        $this->num      = $num;
        $this->oldMoney = $oldMoney;
        $this->newMoney = $newMoney;
    }

    public static function getDescription(): string
    {
        return '帖子-购买';
    }
}
