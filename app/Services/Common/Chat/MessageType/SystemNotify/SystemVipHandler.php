<?php

namespace App\Services\Common\Chat\MessageType\SystemNotify;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Services\Common\Chat\MessageType\ChatMessageTypeHandlerInterface;

/**
 * 系统通知-VIP充值
 * {
 * "content":"充值 VIP 已到账",          //描述
 * "order_sn":"SN20101011123",          //订单号
 * "link": "/post/12345",               // 前端跳转路由
 * }
 */
class SystemVipHandler implements ChatMessageTypeHandlerInterface
{
    public static function getBody(array $msgBody): array
    {
        if (!isset($msgBody['content']) || empty($msgBody['order_sn']) || empty($msgBody['link'])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '消息体结构错误');
        }
        return [
            'content'  => strval($msgBody['content']),
            'order_sn' => strval($msgBody['order_sn']),
            'link'     => strval($msgBody['link']),
        ];
    }

    public static function getPreview(array $msgBody): string
    {
        return strval($msgBody['content']);
    }
}
